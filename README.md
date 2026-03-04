# Timeshow Generator

**Timeshow Generator** is a WordPress plugin designed to automate the creation of time-based content using background workers. It provides a modular generation system, templates, a custom worker engine, logging, and an isolated storage folder for generated output.

---

## Key Features

- Automated time-based generation via WP-Cron or system cron  
- Background worker engine to offload heavy tasks  
- Modular architecture (`/includes`, `/views`, `/workers`)  
- Independent uploads storage inside the plugin folder  
- Worker logging to `worker.log`  
- Extendable internal API for developers  

---

## File Structure

```
timeshow-generator/
├── assets/          # Static assets
├── includes/        # Core logic, helpers, controllers
├── uploads/         # Generated content storage
├── views/           # Rendering templates
├── workers/         # Worker scripts
├── worker.log       # Worker execution logs
├── index.php
└── timeshow-generator.php
```

---

## Installation

1. Upload the plugin folder to:

```
/wp-content/plugins/
```

2. Activate **Timeshow Generator** via  
**WordPress Admin → Plugins**.

3. Ensure the following paths are writable:

- `uploads/`
- `worker.log`

---

## Requirements

- WordPress 5.8 or higher  
- PHP 7.4 or higher  
- Cron support (WP-Cron or system cron)  
- Permission to execute PHP scripts inside `/workers`  

---

## Optional Integrations

### WP-Cron

Workers can be triggered automatically using WP-Cron.

### System Cron (Linux)

For more reliable execution, configure a system cron job:

```bash
php /path/to/wp-content/plugins/timeshow-generator/workers/run.php
```

### External Services

Worker execution can also be triggered from:

- Cloud schedulers  
- CI/CD pipelines  
- Custom automation tools  

---

## API Documentation

The plugin exposes several internal APIs and helper functions for extension and integration.

### Initialization Hook

`timeshow_generator_init`  
Triggered when the plugin is loaded.

```php
do_action('timeshow_generator_init');
```

Use this hook to register custom generators or modify plugin behavior.

---

### Triggering the Worker Manually

`timeshow_generator_run_worker()`  
Executes the worker immediately.

```php
if (function_exists('timeshow_generator_run_worker')) {
    timeshow_generator_run_worker();
}
```

Useful for debugging, manual generation, or external integrations.

---

### Registering a Custom Generator

Custom generation logic can be registered via:

`timeshow_generator_register`

```php
add_action('timeshow_generator_register', function($manager) {
    $manager->addGenerator('my_custom_generator', function() {
        // Custom generation logic
    });
});
```

---

### Template Rendering

Templates stored in `/views/` can be rendered using:

`timeshow_generator_render_view($template, $data = [])`

```php
$content = timeshow_generator_render_view('output-template', [
    'timestamp' => time(),
    'label'     => 'Generated at'
]);
```

Returns the rendered HTML string.

---

### Logging API

`timeshow_generator_log($message)`

```php
timeshow_generator_log('Worker started at ' . date('H:i:s'));
```

Writes log entries to:

```
/timeshow-generator/worker.log
```

---

## Worker System

The worker system is located in the `/workers/` directory and may include:

- Main worker entry file  
- Split worker tasks  
- Cron-triggered execution pipeline  

Workers load logic from `/includes` and templates from `/views`.

---

## Changelog

### 1.0.3 — 2026-01-20

- Added API documentation section  
- Added full changelog section  
- Improved README structure and clarity  
- Minor improvements to file structure documentation  

### 1.0.2

- Added extended logging for worker execution  
- Improved template rendering engine  
- Refactored helper functions  

### 1.0.1

- Fixed uploads directory permission issues  
- Added basic worker status logging  
- Updated plugin headers  

### 1.0.0 — Initial Release

- Core worker system implemented  
- Time-based generator logic added  
- Includes, views, and uploads architecture introduced  
- Basic cron support added  


