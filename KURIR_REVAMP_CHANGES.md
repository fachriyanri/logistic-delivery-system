# Kurir Feature Revamp - Changes Summary

## Overview
This document summarizes all the changes made to revamp the kurir feature according to the requirements.

## Changes Made

### 1. Database Migration
- **File**: `app/Database/Migrations/2025-08-25-000004_DropPasswordFromKurirTable.php`
- **Action**: Created migration to drop `password` column from `kurir` table
- **Reason**: Password will now be stored in `user` table for authentication

### 2. Entity Updates
- **File**: `app/Entities/KurirEntity.php`
- **Changes**:
  - Removed `password` from `$attributes` array
  - Removed `password` from `$casts` array
  - Removed `setPassword()` and `verifyPassword()` methods

### 3. Model Updates
- **File**: `app/Models/KurirModel.php`
- **Changes**:
  - Removed `password` from `$allowedFields` array
  - Removed `password` validation rules
  - Simplified `saveKurir()` method (removed password hashing)
  - Removed `authenticate()` and `updatePassword()` methods
  - Fixed validation rule for `id_kurir` to remove placeholder issue

### 4. Service Updates
- **File**: `app/Services/KurirService.php`
- **Changes**:
  - Added `UserModel` dependency
  - Updated `createCourier()` method to:
    - Validate username and password for new couriers
    - Create user account in `user` table with level 2 (kurir)
    - Use database transactions for data integrity
  - Updated `updateCourier()` method to only update kurir table (not user table)
  - Updated validation to include username and password for new couriers
  - Removed `authenticateCourier()` and `updatePassword()` methods

### 5. Controller Updates
- **File**: `app/Controllers/KurirController.php`
- **Changes**:
  - Added `username` and `password` to data preparation
  - Simplified save action to always redirect to `/kurir` after save
  - Removed `updatePassword()` method

### 6. View Updates
- **File**: `app/Views/kurir/manage.php`
- **Changes**:
  - Added username field (only for new kurir creation)
  - Modified password field (only for new kurir creation)
  - Removed password and confirm password fields for edit mode
  - Removed "Simpan & Tutup" button, kept only "Simpan" button
  - Removed password confirmation validation JavaScript

- **File**: `app/Views/kurir/index.php`
- **Changes**:
  - Added "Username" column header
  - Added placeholder for username display (currently shows "-")
  - Updated colspan for empty state message

## Bug Fixes

### 1. Placeholder Field Error
- **Issue**: "The placeholder field cannot use placeholder: id_kuriri"
- **Fix**: Removed complex validation rule with placeholder from KurirModel
- **File**: `app/Models/KurirModel.php`

## New Features

### 1. User Account Creation
- When creating a new kurir, a corresponding user account is created with:
  - Auto-generated `id_user` (incremented from last user)
  - Username from form input
  - Hashed password using ARGON2ID
  - Level 2 (kurir level)
  - Active status set to true
  - Current timestamp for created_at and updated_at

### 2. Simplified Edit Flow
- Edit mode no longer shows password fields
- Only kurir table data is updated during edit
- User table remains unchanged during kurir edits

### 3. Improved Save Flow
- Single "Simpan" button that redirects to kurir list after save
- Removed redundant "Simpan & Tutup" button
- Consistent redirect behavior for both create and edit

## Database Schema Changes

### Before:
```sql
CREATE TABLE kurir (
    id_kurir VARCHAR(5) PRIMARY KEY,
    nama VARCHAR(30),
    jenis_kelamin VARCHAR(10),
    telepon VARCHAR(15),
    alamat VARCHAR(150),
    password VARCHAR(255),  -- This column will be dropped
    created_at DATETIME,
    updated_at DATETIME
);
```

### After:
```sql
CREATE TABLE kurir (
    id_kurir VARCHAR(5) PRIMARY KEY,
    nama VARCHAR(30),
    jenis_kelamin VARCHAR(10),
    telepon VARCHAR(15),
    alamat VARCHAR(150),
    -- password column removed
    created_at DATETIME,
    updated_at DATETIME
);

-- Authentication handled by user table:
CREATE TABLE user (
    id_user VARCHAR(5) PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    level TINYINT(1), -- 2 for kurir
    is_active TINYINT(1),
    created_at DATETIME,
    updated_at DATETIME
);
```

## Migration Instructions

1. Run the migration to drop password column from kurir table:
   ```bash
   php spark migrate
   ```

2. Test the kurir creation flow:
   - Go to `/kurir/manage`
   - Fill in all fields including username and password
   - Click "Simpan"
   - Verify redirect to `/kurir`
   - Check that both kurir and user records are created

3. Test the kurir edit flow:
   - Go to `/kurir/manage/KRR01`
   - Verify no password fields are shown
   - Update kurir information
   - Click "Simpan"
   - Verify redirect to `/kurir`
   - Check that only kurir record is updated

## Notes

- The username column in the kurir index view currently shows "-" as placeholder
- To display actual usernames, you would need to join with the user table
- All password authentication is now handled through the user table
- The kurir level in user table is set to constant "2" as requested