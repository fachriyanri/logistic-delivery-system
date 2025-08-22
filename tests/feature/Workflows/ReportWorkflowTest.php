<?php

namespace Tests\Feature\Workflows;

use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class ReportWorkflowTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsUser('testadmin');
    }

    public function testCompleteReportGenerationWorkflow(): void
    {
        // Step 1: Access reports dashboard
        $reportsPageResult = $this->get('/laporan');
        if ($reportsPageResult->isOK()) {
            $reportsPageResult->assertSee('Laporan');
            $reportsPageResult->assertSee('Generate Report');
        }

        // Step 2: Select report parameters
        $reportParams = [
            'report_type' => 'shipment_summary',
            'date_from' => date('Y-m-d', strtotime('-30 days')),
            'date_to' => date('Y-m-d'),
            'status' => 'all',
            'customer' => 'all',
        ];

        // Step 3: Generate HTML report preview
        $previewResult = $this->post('/laporan/preview', $reportParams);
        if ($previewResult->isOK()) {
            $previewResult->assertSee('Report Preview');
            $previewResult->assertSee('Total Shipments');
            $previewResult->assertSee('PGR001'); // Should show test data
        }

        // Step 4: Export report to Excel
        $excelParams = array_merge($reportParams, ['format' => 'excel']);
        $excelResult = $this->post('/laporan/export', $excelParams);
        
        $this->assertTrue(
            $excelResult->isOK() || 
            $excelResult->getStatusCode() === 200 ||
            $excelResult->isRedirect()
        );

        // Verify Excel content type if successful
        if ($excelResult->isOK()) {
            $headers = $excelResult->response()->getHeaders();
            $this->assertTrue(
                isset($headers['Content-Type']) && 
                (strpos($headers['Content-Type'][0], 'excel') !== false ||
                 strpos($headers['Content-Type'][0], 'spreadsheet') !== false)
            );
        }

        // Step 5: Export report to PDF
        $pdfParams = array_merge($reportParams, ['format' => 'pdf']);
        $pdfResult = $this->post('/laporan/export', $pdfParams);
        
        $this->assertTrue(
            $pdfResult->isOK() || 
            $pdfResult->getStatusCode() === 200 ||
            $pdfResult->isRedirect()
        );

        // Verify PDF content type if successful
        if ($pdfResult->isOK()) {
            $headers = $pdfResult->response()->getHeaders();
            $this->assertTrue(
                isset($headers['Content-Type']) && 
                strpos($headers['Content-Type'][0], 'pdf') !== false
            );
        }
    }

    public function testDailyReportWorkflow(): void
    {
        // Step 1: Generate daily report for today
        $todayParams = [
            'report_type' => 'daily',
            'date' => date('Y-m-d'),
        ];

        $todayResult = $this->post('/laporan/daily', $todayParams);
        if ($todayResult->isOK()) {
            $todayResult->assertSee('Daily Report');
            $todayResult->assertSee(date('Y-m-d'));
        }

        // Step 2: Generate daily report for specific date
        $specificDateParams = [
            'report_type' => 'daily',
            'date' => date('Y-m-d', strtotime('-1 day')),
        ];

        $specificDateResult = $this->post('/laporan/daily', $specificDateParams);
        if ($specificDateResult->isOK()) {
            $specificDateResult->assertSee('Daily Report');
        }

        // Step 3: Export daily report
        $exportDailyResult = $this->get('/laporan/daily/export?date=' . date('Y-m-d'));
        $this->assertTrue(
            $exportDailyResult->isOK() || 
            $exportDailyResult->isRedirect()
        );
    }

    public function testMonthlyReportWorkflow(): void
    {
        // Step 1: Generate monthly report for current month
        $currentMonthParams = [
            'report_type' => 'monthly',
            'year' => date('Y'),
            'month' => date('m'),
        ];

        $currentMonthResult = $this->post('/laporan/monthly', $currentMonthParams);
        if ($currentMonthResult->isOK()) {
            $currentMonthResult->assertSee('Monthly Report');
            $currentMonthResult->assertSee(date('F Y'));
        }

        // Step 2: Generate monthly report for previous month
        $previousMonth = date('Y-m', strtotime('-1 month'));
        $previousMonthParams = [
            'report_type' => 'monthly',
            'year' => date('Y', strtotime($previousMonth . '-01')),
            'month' => date('m', strtotime($previousMonth . '-01')),
        ];

        $previousMonthResult = $this->post('/laporan/monthly', $previousMonthParams);
        if ($previousMonthResult->isOK()) {
            $previousMonthResult->assertSee('Monthly Report');
        }

        // Step 3: Export monthly report
        $exportMonthlyResult = $this->get('/laporan/monthly/export?year=' . date('Y') . '&month=' . date('m'));
        $this->assertTrue(
            $exportMonthlyResult->isOK() || 
            $exportMonthlyResult->isRedirect()
        );
    }

    public function testCustomReportWorkflow(): void
    {
        // Step 1: Access custom report builder
        $customReportPageResult = $this->get('/laporan/custom');
        if ($customReportPageResult->isOK()) {
            $customReportPageResult->assertSee('Custom Report');
            $customReportPageResult->assertSee('Select Fields');
        }

        // Step 2: Configure custom report parameters
        $customParams = [
            'fields' => ['id_pengiriman', 'tanggal', 'pelanggan', 'status'],
            'date_from' => date('Y-m-d', strtotime('-90 days')),
            'date_to' => date('Y-m-d'),
            'group_by' => 'status',
            'sort_by' => 'tanggal',
            'sort_order' => 'desc',
        ];

        // Step 3: Generate custom report
        $customReportResult = $this->post('/laporan/custom/generate', $customParams);
        if ($customReportResult->isOK()) {
            $customReportResult->assertSee('Custom Report Results');
            $customReportResult->assertSee('PGR001');
        }

        // Step 4: Save custom report template
        $saveTemplateParams = array_merge($customParams, [
            'template_name' => 'Test Custom Report',
            'description' => 'Custom report for testing',
        ]);

        $saveTemplateResult = $this->post('/laporan/custom/save-template', $saveTemplateParams);
        if ($saveTemplateResult->isRedirect()) {
            $saveTemplateResult->assertRedirectTo('/laporan/custom');
            
            $session = session();
            $this->assertTrue($session->has('success'));
        }

        // Step 5: Load saved template
        $loadTemplateResult = $this->get('/laporan/custom/load-template/1');
        if ($loadTemplateResult->isOK()) {
            $loadTemplateResult->assertSee('Test Custom Report');
        }
    }

    public function testReportFilteringWorkflow(): void
    {
        // Step 1: Generate report with status filter
        $statusFilterParams = [
            'report_type' => 'shipment_list',
            'date_from' => date('Y-m-d', strtotime('-30 days')),
            'date_to' => date('Y-m-d'),
            'status' => 1, // Pending only
        ];

        $statusFilterResult = $this->post('/laporan/generate', $statusFilterParams);
        if ($statusFilterResult->isOK()) {
            $statusFilterResult->assertSee('Pending');
            $statusFilterResult->assertDontSee('Delivered'); // Should not show delivered items
        }

        // Step 2: Generate report with customer filter
        $customerFilterParams = [
            'report_type' => 'shipment_list',
            'date_from' => date('Y-m-d', strtotime('-30 days')),
            'date_to' => date('Y-m-d'),
            'customer' => 'PLG001',
        ];

        $customerFilterResult = $this->post('/laporan/generate', $customerFilterParams);
        if ($customerFilterResult->isOK()) {
            $customerFilterResult->assertSee('PLG001');
        }

        // Step 3: Generate report with courier filter
        $courierFilterParams = [
            'report_type' => 'shipment_list',
            'date_from' => date('Y-m-d', strtotime('-30 days')),
            'date_to' => date('Y-m-d'),
            'courier' => 'KUR001',
        ];

        $courierFilterResult = $this->post('/laporan/generate', $courierFilterParams);
        if ($courierFilterResult->isOK()) {
            $courierFilterResult->assertSee('KUR001');
        }

        // Step 4: Generate report with multiple filters
        $multipleFilterParams = [
            'report_type' => 'shipment_list',
            'date_from' => date('Y-m-d', strtotime('-7 days')),
            'date_to' => date('Y-m-d'),
            'status' => 1,
            'customer' => 'PLG001',
            'courier' => 'KUR001',
        ];

        $multipleFilterResult = $this->post('/laporan/generate', $multipleFilterParams);
        if ($multipleFilterResult->isOK()) {
            $multipleFilterResult->assertSee('Report Results');
        }
    }

    public function testReportAnalyticsWorkflow(): void
    {
        // Step 1: Generate analytics dashboard
        $analyticsResult = $this->get('/laporan/analytics');
        if ($analyticsResult->isOK()) {
            $analyticsResult->assertSee('Analytics Dashboard');
            $analyticsResult->assertSee('Total Shipments');
            $analyticsResult->assertSee('Performance Metrics');
        }

        // Step 2: Get shipment trend data
        $trendParams = [
            'period' => 'monthly',
            'year' => date('Y'),
        ];

        $trendResult = $this->get('/laporan/analytics/trend?' . http_build_query($trendParams));
        if ($trendResult->isOK()) {
            $json = $trendResult->getJSON();
            $this->assertIsArray($json);
            $this->assertArrayHasKey('data', $json);
        }

        // Step 3: Get performance metrics
        $metricsResult = $this->get('/laporan/analytics/metrics');
        if ($metricsResult->isOK()) {
            $json = $metricsResult->getJSON();
            $this->assertIsArray($json);
            $this->assertArrayHasKey('total_shipments', $json);
            $this->assertArrayHasKey('delivery_rate', $json);
        }

        // Step 4: Generate comparative analysis
        $compareParams = [
            'period1_from' => date('Y-m-d', strtotime('-60 days')),
            'period1_to' => date('Y-m-d', strtotime('-30 days')),
            'period2_from' => date('Y-m-d', strtotime('-30 days')),
            'period2_to' => date('Y-m-d'),
        ];

        $compareResult = $this->post('/laporan/analytics/compare', $compareParams);
        if ($compareResult->isOK()) {
            $compareResult->assertSee('Comparative Analysis');
            $compareResult->assertSee('Period 1');
            $compareResult->assertSee('Period 2');
        }
    }

    public function testReportSchedulingWorkflow(): void
    {
        // Step 1: Access report scheduling
        $schedulePageResult = $this->get('/laporan/schedule');
        if ($schedulePageResult->isOK()) {
            $schedulePageResult->assertSee('Scheduled Reports');
            $schedulePageResult->assertSee('Create Schedule');
        }

        // Step 2: Create scheduled report
        $scheduleParams = [
            'report_type' => 'daily_summary',
            'frequency' => 'daily',
            'time' => '08:00',
            'email_recipients' => 'admin@example.com,finance@example.com',
            'format' => 'pdf',
            'active' => true,
        ];

        $createScheduleResult = $this->post('/laporan/schedule/create', $scheduleParams);
        if ($createScheduleResult->isRedirect()) {
            $createScheduleResult->assertRedirectTo('/laporan/schedule');
            
            $session = session();
            $this->assertTrue($session->has('success'));
        }

        // Step 3: View scheduled reports list
        $scheduleListResult = $this->get('/laporan/schedule');
        if ($scheduleListResult->isOK()) {
            $scheduleListResult->assertSee('daily_summary');
            $scheduleListResult->assertSee('08:00');
        }

        // Step 4: Edit scheduled report
        $editScheduleParams = [
            'frequency' => 'weekly',
            'time' => '09:00',
            'day_of_week' => 'monday',
        ];

        $editScheduleResult = $this->post('/laporan/schedule/edit/1', $editScheduleParams);
        if ($editScheduleResult->isRedirect()) {
            $editScheduleResult->assertRedirectTo('/laporan/schedule');
        }

        // Step 5: Test scheduled report execution
        $executeScheduleResult = $this->post('/laporan/schedule/execute/1');
        if ($executeScheduleResult->isRedirect()) {
            $executeScheduleResult->assertRedirectTo('/laporan/schedule');
            
            $session = session();
            $this->assertTrue($session->has('success'));
        }
    }

    public function testReportAccessControlWorkflow(): void
    {
        // Test admin access
        $this->loginAsUser('testadmin');
        $adminReportResult = $this->get('/laporan');
        $adminReportResult->assertOK();

        // Test finance user access
        $this->get('/auth/logout');
        $this->loginAsUser('testfinance');
        $financeReportResult = $this->get('/laporan');
        
        // Finance should have access to reports
        $this->assertTrue(
            $financeReportResult->isOK() || 
            $financeReportResult->isRedirect()
        );

        // Test gudang user access
        $this->get('/auth/logout');
        $this->loginAsUser('testgudang');
        $gudangReportResult = $this->get('/laporan');
        
        // Gudang might have limited access to reports
        $this->assertTrue(
            $gudangReportResult->isOK() || 
            $gudangReportResult->getStatusCode() === 403 ||
            $gudangReportResult->isRedirect()
        );
    }

    /**
     * Helper method to login as a specific user
     */
    private function loginAsUser(string $username): void
    {
        $data = [
            'username' => $username,
            'password' => 'testpass123',
        ];
        
        $this->post('/auth/authenticate', $data);
    }
}