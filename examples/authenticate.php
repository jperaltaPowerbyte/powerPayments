<?php

use PowerPayments\PowerPayments;

require '../PowerPayments.php';

//Send credentials to the sdk server for authentication.
$powerPayments = new PowerPayments('test@user.com', 'JColombres324', 'e99766d6901328420c870b79186eb36a');

//Set enviroment sandbox true or false
$powerPayments->setEnvironment(false);

