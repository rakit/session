Rakit Session
==========

PHP Session Manager

## Features

* Flash support
* PDO Session handler
* Cookie Session handler with encrypted cookie (optional)
* File Session handler instead Native

## Installation

There is two way to install this library

#### 1) Using Composer

add this repository in your `composer.json` file, and then require it

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://emsifa@bitbucket.org/emsifa/rakit-session.git"
        }
    ],
    "require": {
        "rakit/session": "dev-master"
    }
}
```

#### 2) Manual

* [Download](https://bitbucket.org/emsifa/rakit-session/downloads) this repository. 
* Put it somewhere in your project directory
* Require/include `SessionManager.php` and session handler that you need.

## Examples

look at [samples folder](https://bitbucket.org/emsifa/rakit-session/src/master/samples/?at=master)