Cruftman developer's notes
--------------------------

Must-have software
^^^^^^^^^^^^^^^^^^

These are required to do initialize stuff

- composer_

These are highly recommended for behavioral testing, generating docs, etc.

- docker_
- vagrant_
- virtualbox_


Initial preparations
^^^^^^^^^^^^^^^^^^^^

If you've just cloned the project, then

.. code-block:: shell

   composer install --dev                 # installs required PHP packages
   php docker/initialize                  # initializes docker settings in docker/.env
   cp homestead/Homestead.yaml{.example,} # and edit homestead/Homestead.yaml to adjust its settins to your needs


More information:

- ``Homestead.yaml`` is documented at `Homestead documentation`_


Running unit tests
^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   php vendor/bin/phpunit


Starting interactive PHP shell
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   php artisan tinker


Running integration tests
^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   docker/compose run --rm test vendor/bin/behat


Testing examples in documentation
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   docker/compose run --rm test vendor/bin/behat -c docs/behat.yml


Running CodeClimate
^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   docker/codeclimate run --rm codeclimate analyze


Generating user documentation
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   docker/docs run --rm sphinx build

The generated API docs go to ``docs/build/html/``.


Generating API documentation
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   docker/docs run --rm sami build

The generated API docs go to ``docs/build/html/api/``.


Generating and serving documentation continuously
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   docker/docs up

The generated docs go to ``docs/build/html`` and get exposed at

- ``http://localhost:8002`` -> sphinx docs (user manual, etc),
- ``http://localhost:8001`` -> sami docs (PHP API)


Generating (only) user documentation continuously
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   docker/docs up sphinx

The generated docs go to ``docs/build/html`` and get exposed at

- ``http://localhost:8002``.


Generating (only) API documentation continuously
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

   docker/docs up sami

The generated API docs go to ``docs/build/html/api/`` and get exposed at

- ``https://localhost:8001``.

.. _docker: https://docker.com/
.. _vagrant: https://vagrantup.com/
.. _virtualbox: https://www.virtualbox.org/
.. _composer: https://getcomposer.org/
.. _Homestead documentation: https://laravel.com/docs/homestead/
.. _Compose command-line reference: https://docs.docker.com/compose/reference/

.. <!--- vim: set syntax=rst spell: -->
