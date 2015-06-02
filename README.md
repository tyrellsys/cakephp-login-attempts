# LoginAttempts plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require nojimage/cakephp-LoginAttempts
```

### use LoginAttempts.FormAuthenticate
```
        $this->loadComponent('Auth', [
            'authenticate' => [
                'LoginAttempts.Form' => [
                    'fields' => ['username' => 'email'],
                    'attemptLimit' => 5,
                    'attemptDuration' => '+5 minutes',
                ],
            ],
        ]);
```
