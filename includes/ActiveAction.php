<?php

namespace RHC\includes;

/**
 * This file contains the main class for handling the plugin's activation and deactivation actions.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The plugin activation and deactivation action class.
 *
 * This class handles the actions that should be executed when the plugin is activated or deactivated.
 */
class ActiveAction
{

    /**
     * Handles the plugin activation process.
     *
     * This method is triggered when the plugin is activated. It can be used to set up
     * necessary resources, database tables, default settings, etc.
     *
     * @return void
     */
    public function activate(): void
    {
        // Add your activation logic here
    }

    /**
     * Handles the plugin deactivation process.
     *
     * This method is triggered when the plugin is deactivated. It can be used to clean up resources,
     * such as removing database tables, clearing caches, or resetting options.
     *
     * @return void
     */
    public function deactivate(): void
    {
        // Add your deactivation logic here
    }
}
