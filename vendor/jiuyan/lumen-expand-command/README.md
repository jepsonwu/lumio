# `vendor:publish make:request make:provider` for Lumen framework


This package contains a single command that enables you to publish a package config file to the config folder of your Lumen application.

## Installation

```
composer require Jiuyan/lumen-expand-command
```

## Usage

To be able to use it you have to add it to your `app/Console/Kernel.php` file:

```
protected $commands = [
    \Jiuyan\\LumenCommand\VendorPublishCommand::class,
    \Jiuyan\\LumenCommand\ProviderMakeCommand::class,
    \Jiuyan\\LumenCommand\RequestMakeCommand::class
];
```
