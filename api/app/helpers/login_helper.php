<?php

function isLoggedIn()
{
    if (isset($_SESSION['gUserId'])) {
        return true;
    } else {
        return false;
    }

}

function checkPermission($permissionsArray, $permissionName): bool
{

//    $permissions = array_column($permissionsArray, 'pName');
    return in_array($permissionName, $permissionsArray);
}