<?php

namespace MBComponents;

return array(
    'service_manager' => array(
        'factories' => array(
            'MBComponents\Service\IntegrityService' => 'MBComponents\Service\Factory\IntegrityServiceFactory'
        ),
        'invokables' => array(
        ),
    ),
);
