### Using ddev

The project can be run/tested using docker containers maintained with
[ddev](https://ddev.com/). There are preconfigured containers with web server,
database, and other useful tools such as [phpmyadmin](https://www.phpmyadmin.net/) .

#### Starting ddev containers (optionally)

```console
ddev start
```

#### Installing dependencies

```console
ddev composer install
```

#### Opening the website

```console
ddev launch
```

Alternatively, you may just open you browser and navigate to https://cruftman.ddev.site.

#### Using symfony CLI

```console
ddev symfony help
```

#### Using bin/console
```console
ddev console help
```

#### Using phpunit

```console
ddev phpunit --help
```

#### Running psalm

```console
ddev psalm --help
```

#### Launching phpmyadmin

```console
ddev phpmyadmin
```

#### Customizing containers

Override ddev [config options](https://ddev.readthedocs.io/en/stable/users/configuration/config/) &mdash; create [.ddev/config.local.yaml](https://ddev.readthedocs.io/en/stable/users/configuration/config/#environmental-overrides) with your environmental overrides.

```yml
# .ddev/config.local.yml
router_http_port: 8000
```
