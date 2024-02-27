<?php

function jet_breeze_resources_path(string $path): string
{
    return jet_base_path(
        'breeze'
        .DIRECTORY_SEPARATOR
        .'resources'
        .($path ? DIRECTORY_SEPARATOR.$path : $path)
    );
}

function jet_breeze_inertia_path($path = ''): string
{
    return jet_base_path(
        'breeze'
        .DIRECTORY_SEPARATOR
        .'inertia'
        .($path ? DIRECTORY_SEPARATOR.$path : $path)
    );
}

/**
 * @param string $path
 * @return string
 */
function jet_stubs_inertia_resources(string $path = ''): string
{
    return jet_stubs_path(
        'inertia'
        .DIRECTORY_SEPARATOR
        .'resources'
        .($path ? DIRECTORY_SEPARATOR.$path : $path)
    );

}

/**
 * @param string $path
 * @return string
 */
function jet_stubs_livewire(string $path = ''): string
{
    return jet_stubs_path(
        'livewire'
        .DIRECTORY_SEPARATOR
        .'resources'
        .DIRECTORY_SEPARATOR
        .'views'
        .($path ? DIRECTORY_SEPARATOR.$path : $path)
    );
}

/**
 * @param $path
 * @return string
 */
function jet_stubs_resources_path($path = ''): string
{
    return jet_stubs_path(
        'resources'
        .($path ? DIRECTORY_SEPARATOR.$path : $path)
    );

}

function jet_stubs_path($path = ''): string
{
    return __DIR__ . DIRECTORY_SEPARATOR
        .'..'
        .DIRECTORY_SEPARATOR
        .'stubs'
        .($path ? DIRECTORY_SEPARATOR.$path : $path);
}

/**
 * @param $path
 * @return string
 */
function jet_resource_path($path = ''): string
{
    return __DIR__ . DIRECTORY_SEPARATOR
        .'..'
        .DIRECTORY_SEPARATOR
        .'resources'
        .($path ? DIRECTORY_SEPARATOR.$path : $path);
}

/**
 * @param $path
 * @return string
 */
function jet_base_path($path = ''): string
{
    return __DIR__ . DIRECTORY_SEPARATOR
        .'..'
        .($path ? DIRECTORY_SEPARATOR.$path : $path);
}