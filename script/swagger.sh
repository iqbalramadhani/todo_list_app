

#!/bin/bash
php artisan l5-swagger:generate

php artisan migrate --path=/database/migrations/fileName.php

printf "Protobuf created successfully\n"