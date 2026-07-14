# Creación de un Nuevo Proyecto

## Índice

1. Preparación del entorno
2. Creación del proyecto
3. Configuración de la base de datos
4. Configuración de GitHub
5. Configuración del entorno de desarrollo

## Verificación final

Debe cumplirse:

- El sitio responde correctamente por HTTPS.
- php artisan about ejecuta sin errores.
- php artisan migrate informa "Nothing to migrate".
- git status indica "working tree clean".
- Los breakpoints funcionan desde VS Code.

docs/
├── Creación de un Nuevo Proyecto.md
├── Convenciones de Desarrollo.md
├── Arquitectura.md
├── Base de Datos.md
├── Despliegue.md
├── Git.md
└── Changelog.md

=============================================================================================================================
*** CREACIÓN DE UN NUEVO PROYECTO A PARTIR DE LA PLANTILLA 

	1) Creando hostname en archivo hosts.

		- C:\Windows\System32\drivers\etc\hosts
			127.0.0.1 applicationbase.lenovo-notebook.cl

	2) Definiendo VirtualHosts.

		- VirtualHost Puerto 80

			# Virtual Host para applicationbase.lenovo-notebook.cl (HTTP)
			<VirtualHost *:80>
				ServerName applicationbase.lenovo-notebook.cl

				# Redirigir HTTP a HTTPS
				RewriteEngine On
				RewriteCond %{HTTPS} !=on
				RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

				# Logs para depuración
				ErrorLog "logs/applicationbase-error.log"
				CustomLog "logs/applicationbase-access.log" combined
			</VirtualHost>

		- VirtualHost Puerto 443

			# Virtual Host para applicationbase.lenovo-notebook.cl (HTTP)
			<VirtualHost *:443>
				ServerName applicationbase.lenovo-notebook.cl

				DocumentRoot "D:/Apps-Apache-htdocs/applicationbase/public"
				<Directory "D:/Apps-Apache-htdocs/applicationbase/public">
					Options Indexes Multiviews FollowSymLinks
					AllowOverride All
					Require all granted
				</Directory>

				# Habilita SSL y configura el certificado comodín para HTTPS.
				SSLEngine on
				SSLCertificateFile "conf/ssl.crt/wildcard.crt"
				SSLCertificateKeyFile "conf/ssl.key/wildcard.key"

				# Logs para depuración
				ErrorLog "logs/applicationbase-ssl-error.log"
				CustomLog "logs/applicationbase-ssl-access.log" combined
			</VirtualHost>	

	3) Copiando el proyecto de origen al proyecto destino.

		- Copiamos D:\Apps-Apache-htdocs\laportada-v2 a D:\Apps-Apache-htdocs\applicationbase

		- Limpiamos Laravel.

			$> cd /d D:\PHP-Laravel\ApplicationBase
			$> limpia-laravel.bat

		- Ajustar valores en archivo de configuración '.env'

			APP_URL=https://applicationbase.lenovo-notebook.cl
			DB_DATABASE=db_applicationbase

		- Regeneramos la APP_KEY

			APP_KEY=base64:gxFWMosrilfViXdLa02OxL2U2hJOuouBUkcQXZ5Il74=

			$> php8 artisan key:generate

				INFO  Application key set successfully.

			APP_KEY=base64:NGLESYw4demQZcWPycapoxZ4xRjCulKRaHuv3QscwF0=

	4) Clonando la base de datos.

		- Extracción de dump del original

			$> cd /d D:\PHP-Laravel\ApplicationBase
			$> mysqldump -u root -p db_laportada_v2 > db_laportada_v2_2026-07-09.sql

		- Restauración en la base destino

			$> cd /d D:\PHP-Laravel\ApplicationBase
			$> restaura_dump_applicationbase.bat

	5) Verficación de las migraciones.

		$> php8 artisan migrate:status
		
		$> php8 artisan migrate

			INFO  Nothing to migrate.

	6) Chequeo de salud inicial

		$> php8 artisan about

			Environment .........................................................................................................
			Application Name .................................................................................... ApplicationBase
			Laravel Version ............................................................................................. 12.13.0
			PHP Version .................................................................................................. 8.2.12
			Composer Version .................................................................................................. -
			Environment ................................................................................................... local
			Debug Mode .................................................................................................. ENABLED
			URL .............................................................................. applicationbase.lenovo-notebook.cl
			Maintenance Mode ................................................................................................ OFF
			Timezone ........................................................................................... America/Santiago
			Locale ........................................................................................................... es

			Cache ...............................................................................................................
			Config ....................................................................................................... CACHED
			Events ................................................................................................... NOT CACHED
			Routes ................................................................................................... NOT CACHED
			Views .................................................................................................... NOT CACHED

			Drivers .............................................................................................................
			Broadcasting ................................................................................................... null
			Cache .......................................................................................................... file
			Database ...................................................................................................... mysql
			Logs .................................................................................................. stack / daily
			Mail ............................................................................................................ log
			Queue ...................................................................................................... database
			Session .................................................................................................... database

			Storage .............................................................................................................
			D:\Apps-Apache-htdocs\applicationbase\public\storage ..................................................... NOT LINKED


=============================================================================================================================
*** VINCULACION A NUEVO REPOSITORIO DE GIT-HUB

	1) Verificar el estado actual

		$> git status

			Refresh index: 100% (488/488), done.
			On branch v2
			Your branch is up to date with 'origin/v2'.

			Changes not staged for commit:
			  (use "git add <file>..." to update what will be committed)
			  (use "git restore <file>..." to discard changes in working directory)
			        modified:   bootstrap/cache/.gitignore

			no changes added to commit (use "git add" and/or "git commit -a")

		$> git remote -v

			origin  https://jaratran:ghp_ ... @github.com/jaratran/LaPortada-desde-WorkFlow-en-Laravel-12-desde-Cero.git (fetch)
			origin  https://jaratran:ghp_ ... @github.com/jaratran/LaPortada-desde-WorkFlow-en-Laravel-12-desde-Cero.git (push)

	2) Eliminar el repositorio Git heredado

		$> cd /d D:\PHP-Laravel\ApplicationBase
		$> rmdir /s /q .git

	3) Comprobamos que el repositorio Git heredado realmente desapareció
		
		$> git status

			fatal: not a git repository (or any of the parent directories): .git

	4) Inicializar un nuevo repositorio

		$> git init

			Initialized empty Git repository in D:/Apps-Apache-htdocs/applicationbase/.git/

		$> git status

			On branch main

			No commits yet

			Untracked files:
			  (use "git add <file>..." to include in what will be committed)
			        .editorconfig
			        .env.example
			        .gitattributes
			        .gitignore
			        README.md
			        app/
			        artisan
			        bootstrap/
			        clean-logs.php
			        clean-logs.sh
			        composer.json
			        composer.lock
			        config/
			        cron-run-scheduler.php
			        database/
			        deploy.bat
			        deploy.sh
			        limpia-laravel.bat
			        limpia-laravel.sh
			        optimiza-laravel.bat
			        package.json
			        phpunit.xml
			        public/
			        resources/
			        routes/
			        run-laravel-scheduler.bat
			        storage/
			        tests/
			        vite.config.js

			nothing added to commit but untracked files present (use "git add" to track)

	5) Crear el repositorio en GitHub
		Nombre : applicationbase
		Descripción : Base template for Laravel applications.

	6) Vincular la carpeta del proyecto con el nuevo repositorio en GitHub
		- NO usar nombre en CamelCase, acá en el paso 9 lo tuvimos que corregir.

		$> git remote add origin https://github.com/jaratran/ApplicationBase.git

		$> git remote -v

			origin  https://github.com/jaratran/ApplicationBase.git (fetch)
			origin  https://github.com/jaratran/ApplicationBase.git (push)

	7) Primer commit

		$> git add .

			warning: in the working copy of 'bootstrap/cache/.gitignore', CRLF will be replaced by LF the next time Git touches it

		$> git commit -m "Initial commit"

			[main (root-commit) 6cf9106] Initial commit
			 364 files changed, 58488 insertions(+)
			 create mode 100644
			 :
			 :

	8) Publicar

		- Como usaremos la rama main:

			$> git branch -M main

			$> git push -u origin main

				Enumerating objects: 419, done.
				Counting objects: 100% (419/419), done.
				Delta compression using up to 4 threads
				Compressing objects: 100% (401/401), done.
				Writing objects: 100% (419/419), 2.90 MiB | 1.16 MiB/s, done.
				Total 419 (delta 109), reused 0 (delta 0), pack-reused 0 (from 0)
				remote: Resolving deltas: 100% (109/109), done.

				remote: This repository moved. Please use the new location:
				remote:   https://github.com/jaratran/applicationbase.git

				To https://github.com/jaratran/ApplicationBase.git
				 * [new branch]      main -> main
				branch 'main' set up to track 'origin/main'.


	9 ) Corrección de nombre de repositorio según observación remote de paso anterior.
		- NO debe ser CamelCase.
		- Acá en paso 6 usamos CamelCase.

			$> git remote -v

				origin  https://github.com/jaratran/ApplicationBase.git (fetch)
				origin  https://github.com/jaratran/ApplicationBase.git (push)

			$> git remote set-url origin https://github.com/jaratran/applicationbase.git

			$> git remote -v

				origin  https://github.com/jaratran/applicationbase.git (fetch)
				origin  https://github.com/jaratran/applicationbase.git (push)

	10) Verificación de status.

		$> git status

			On branch main
			Your branch is up to date with 'origin/main'.

			nothing to commit, working tree clean


=============================================================================================================================
*** CONFIGURACIÓN DEL ENTORNO DE DESARROLLO

    1) Configuración de Xdebug.

		- Creamos carpeta de configuración de VSCode D:\Apps-Apache-htdocs\applicationbase\.vscode

			- launch.json
				{
				  "version": "0.2.0",
				  "configurations": [
				    {
				      "name": "Listen for Xdebug",
				      "type": "php",
				      "request": "launch",
				      "port": 9003,
				      "pathMappings": {
				        "D:/Apps-Apache-htdocs/applicationbase": "${workspaceFolder}"
				      },
				      "log": true
				    }
				  ]
				}

			- settings.json
				// This file contains settings for the Visual Studio Code editor.
				// It is used to configure various aspects of the editor, such as formatting, linting, and debugging.

				{
				  "php.validate.executablePath": "C:\\xampp-8.2.12\\php\\php8.exe",
				  "php.suggest.basic": false,

				  "intelephense.environment.phpVersion": "8.2",

				  "php.debug.idekey": "VSCODE",
				  "php.debug.executablePath": "C:\\xampp-8.2.12\\php\\php8.exe",
				  "php.debug.port": 9003,
				  "php.debug.remoteHost": "localhost",
				  "php.debug.remotePort": 9003,
				  "php.debug.remoteAutostart": true,
				  "php.debug.remoteConnectBack": false,

				  "workbench.colorTheme": "Monokai",
				  "workbench.colorCustomizations": {
				    "activityBar.activeBackground": "#1f6fd0",
				    "activityBar.background": "#1f6fd0",
				    "activityBar.foreground": "#e7e7e7",
				    "activityBar.inactiveForeground": "#e7e7e799",
				    "activityBarBadge.background": "#ee90bb",
				    "activityBarBadge.foreground": "#15202b",
				    "commandCenter.border": "#e7e7e799",
				    "sash.hoverBorder": "#1f6fd0",
				    "statusBar.background": "#1857a4",
				    "statusBar.foreground": "#e7e7e7",
				    "statusBarItem.hoverBackground": "#1f6fd0",
				    "statusBarItem.remoteBackground": "#1857a4",
				    "statusBarItem.remoteForeground": "#e7e7e7",
				    "titleBar.activeBackground": "#1857a4",
				    "titleBar.activeForeground": "#e7e7e7",
				    "titleBar.inactiveBackground": "#1857a499",
				    "titleBar.inactiveForeground": "#e7e7e799"
				  },
				  "peacock.color": "#1857a4"
				}

		- Creamos archivo phpinfo.php y lo ubicamos en : D:\Apps-Apache-htdocs\applicationbase\public

			<?php
			phpinfo();

		- Buscamos lo siguiente en la dirección: https://applicationbase.lenovo-notebook.cl/phpinfo.php

			Loaded Configuration File 	: C:\xampp-8.2.12\php\php.ini
			extension_dir				: C:\xampp-8.2.12\php\ext / C:\xampp-8.2.12\php\ext

			PHP Version					: 8.2.12
			Thread Safety				: enabled
			Architecture				: x64
			Compiler					: Visual C++ 2019

		- Determina la versión de Xdebug que se debe instalar.
			- Pega la salida completa del phpInfo en el cuadro de https://xdebug.org/wizard
			- Baja la versión sugerida, copiala en C:\xampp-8.2.12\php\ext
			- Renombrala con el nombre php_xdebug.dll

		- Al final de tu C:\xampp-8.2.12\php\php.ini agrega:

			Observación:
				Aunque trigger es la configuración recomendada por Xdebug para activar la depuración solo bajo demanda,
				en este entorno los breakpoints no se activaron correctamente.
				Se optó por yes, priorizando un funcionamiento confiable del entorno de desarrollo.

			; ------------------------------------------------------------
			; Xdebug
			; ------------------------------------------------------------
			[XDebug]
			zend_extension="C:\xampp-8.2.12\php\ext\php_xdebug.dll"

			xdebug.mode=develop,debug
			; Cambiamos yes por trigger para que Xdebug solo se active cuando estás depurando desde VS Code
			; xdebug.start_with_request=trigger
			; Lo volvemos a yes porque con trigger no se activan los breakpoints
			xdebug.start_with_request=yes

			xdebug.client_host=127.0.0.1
			xdebug.client_port=9003
			xdebug.idekey=VSCODE

			;xdebug.log="C:\xampp-8.2.12\tmp\xdebug.log"
			;xdebug.log_level=7

			; Establecemos en 50 milisegundos la espera entre xdebug y VSCode.
			xdebug.connect_timeout_ms=50

		- Reiniciamos Apache y verificamos si quedó instalada la libería:

			$> php8 -v

				PHP 8.2.12 (cli) (built: Oct 24 2023 21:15:15) (ZTS Visual C++ 2019 x64)
				Copyright (c) The PHP Group
				Zend Engine v4.2.12, Copyright (c) Zend Technologies
				    with Zend OPcache v8.2.12, Copyright (c), by Zend Technologies
				    with Xdebug v3.5.3, Copyright (c) 2002-2026, by Derick Rethans

			$> php8 --ri xdebug

				xdebug

				Version => 3.5.3
				Support Xdebug on Patreon, GitHub, or as a business: https://xdebug.org/support
				:
				:
				:

    2) Configuración de Visual Studio Code.

		- Creamos carpeta de configuración de VSCode D:\Apps-Apache-htdocs\applicationbase\.vscode

			- extensions.json
				{
				    "recommendations": [
				        "ryannaddy.laravel-artisan",
				        "onecentlin.laravel5-snippets",
				        "amiralizadeh9480.laravel-extra-intellisense",
				        "xdebug.php-debug",
				        "bmewburn.vscode-intelephense-client",
				        "mikestead.dotenv"
				    ]
				}

		- Reiniciar VSCode para que refresque extensiones recomendadas y ofrezca instalar las que falten.

			Laravel snippets for Visual Studio Code (Support Laravel 5 and above)

		- Modificar el .gitignore de la raiz cambiando:

			/.vscode

			Por:

			# VS Code/
			/.vscode/*
			!/.vscode/launch.json
			!/.vscode/settings.json
			!/.vscode/extensions.json		

		- Eliminar el archivo: public\phpinfo.php

		- Versionar en el repositorio:

			$> git status

				On branch main
				Your branch is up to date with 'origin/main'.

				Changes not staged for commit:
				  (use "git add <file>..." to update what will be committed)
				  (use "git restore <file>..." to discard changes in working directory)
				        modified:   .gitignore

				Untracked files:
				  (use "git add <file>..." to include in what will be committed)
				        .vscode/

				no changes added to commit (use "git add" and/or "git commit -a")

			$> git add .gitignore

			$> git add .vscode

				warning: in the working copy of '.vscode/extensions.json', CRLF will be replaced by LF the next time Git touches it
				warning: in the working copy of '.vscode/launch.json', CRLF will be replaced by LF the next time Git touches it
				warning: in the working copy of '.vscode/settings.json', CRLF will be replaced by LF the next time Git touches it

			$> git status

				On branch main
				Your branch is up to date with 'origin/main'.

				Changes to be committed:
				  (use "git restore --staged <file>..." to unstage)
				        modified:   .gitignore
				        new file:   .vscode/extensions.json
				        new file:   .vscode/launch.json
				        new file:   .vscode/settings.json

			$> git commit -m "Configure VS Code workspace and Xdebug support"

				[main 90148cc] Configure VS Code workspace and Xdebug support
				 4 files changed, 71 insertions(+), 1 deletion(-)
				 create mode 100644 .vscode/extensions.json
				 create mode 100644 .vscode/launch.json
				 create mode 100644 .vscode/settings.json


			$> git push

				Enumerating objects: 9, done.
				Counting objects: 100% (9/9), done.
				Delta compression using up to 4 threads
				Compressing objects: 100% (7/7), done.
				Writing objects: 100% (7/7), 1.40 KiB | 287.00 KiB/s, done.
				Total 7 (delta 2), reused 0 (delta 0), pack-reused 0 (from 0)
				remote: Resolving deltas: 100% (2/2), completed with 2 local objects.
				To https://github.com/jaratran/applicationbase.git
				   6cf9106..90148cc  main -> main

=============================================================================================================================
*** Agregamos este documento en la carpeta /docs y lo subimos al repositorio.


=============================================================================================================================
*** Agregamos el DUMP al consolidado

	$> git add docs

		warning: in the working copy of 'docs/db_laportada_v2_2026-07-09.sql', CRLF will be replaced by LF the next time Git touches it
		warning: in the working copy of 'docs/restaura_dump_applicationbase.bat', CRLF will be replaced by LF the next time Git touches it

	$> git commit -m "Add initial database dump"

		[main 62227a8] Add initial database dump
		 2 files changed, 1110 insertions(+)
		 create mode 100644 docs/db_laportada_v2_2026-07-09.sql
		 create mode 100644 docs/restaura_dump_applicationbase.bat

	$> git push

		Enumerating objects: 7, done.
		Counting objects: 100% (7/7), done.
		Delta compression using up to 4 threads
		Compressing objects: 100% (5/5), done.
		Writing objects: 100% (5/5), 120.90 KiB | 4.03 MiB/s, done.
		Total 5 (delta 1), reused 0 (delta 0), pack-reused 0 (from 0)
		To https://github.com/jaratran/applicationbase.git
		   f3ac442..62227a8  main -> main

=============================================================================================================================
*** Crear un Release 1.0.0 en GitHub con un nombre como: ApplicationBase 1.0.0 - Initial Baseline


-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
=============================================================================================================================
***

-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
=============================================================================================================================
***

-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------
