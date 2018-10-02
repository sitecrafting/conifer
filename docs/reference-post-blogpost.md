
Warning: Reflection::export(): ReflectionParameter::__toString() did not return anything in /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php on line 318

Call Stack:
    0.0007     353808   1. {main}() /app/vendor/victorjonsson/markdowndocs/bin/phpdoc-md:0
    0.0105     878792   2. PHPDocsMD\Console\CLI->run() /app/vendor/victorjonsson/markdowndocs/bin/phpdoc-md:15
    0.0235    1339000   3. PHPDocsMD\Console\CLI->run() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Console/CLI.php:29
    0.0402    1588936   4. PHPDocsMD\Console\CLI->doRun() /app/vendor/symfony/console/Application.php:148
    0.0404    1588936   5. PHPDocsMD\Console\CLI->doRunCommand() /app/vendor/symfony/console/Application.php:248
    0.0405    1588936   6. PHPDocsMD\Console\PHPDocsMDCommand->run() /app/vendor/symfony/console/Application.php:946
    0.0417    1593984   7. PHPDocsMD\Console\PHPDocsMDCommand->execute() /app/vendor/symfony/console/Command/Command.php:255
    0.0500    1997328   8. PHPDocsMD\Console\PHPDocsMDCommand->getClassEntity() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Console/PHPDocsMDCommand.php:107
    0.0552    2112032   9. PHPDocsMD\Reflector->getClassEntity() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Console/PHPDocsMDCommand.php:36
    0.0584    2160048  10. PHPDocsMD\Reflector->getClassFunctions() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:73
    0.0604    2194544  11. PHPDocsMD\Reflector->createFunctionEntity() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:94
    0.0608    2196640  12. PHPDocsMD\Reflector->getParams() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:135
    0.0608    2197624  13. PHPDocsMD\Reflector->createParameterEntity() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:412
    0.0608    2197624  14. PHPDocsMD\Reflector::getParamType() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:226
    0.0608    2198000  15. ReflectionParameter::export() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:318
    0.0608    2198184  16. Reflection::export() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:318


Fatal error: Uncaught Error: Undefined class constant 'self::NUM_RELATED_POSTS' in /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php on line 318

Error: Undefined class constant 'self::NUM_RELATED_POSTS' in /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php on line 318

Call Stack:
    0.0007     353808   1. {main}() /app/vendor/victorjonsson/markdowndocs/bin/phpdoc-md:0
    0.0105     878792   2. PHPDocsMD\Console\CLI->run() /app/vendor/victorjonsson/markdowndocs/bin/phpdoc-md:15
    0.0235    1339000   3. PHPDocsMD\Console\CLI->run() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Console/CLI.php:29
    0.0402    1588936   4. PHPDocsMD\Console\CLI->doRun() /app/vendor/symfony/console/Application.php:148
    0.0404    1588936   5. PHPDocsMD\Console\CLI->doRunCommand() /app/vendor/symfony/console/Application.php:248
    0.0405    1588936   6. PHPDocsMD\Console\PHPDocsMDCommand->run() /app/vendor/symfony/console/Application.php:946
    0.0417    1593984   7. PHPDocsMD\Console\PHPDocsMDCommand->execute() /app/vendor/symfony/console/Command/Command.php:255
    0.0500    1997328   8. PHPDocsMD\Console\PHPDocsMDCommand->getClassEntity() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Console/PHPDocsMDCommand.php:107
    0.0552    2112032   9. PHPDocsMD\Reflector->getClassEntity() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Console/PHPDocsMDCommand.php:36
    0.0584    2160048  10. PHPDocsMD\Reflector->getClassFunctions() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:73
    0.0604    2194544  11. PHPDocsMD\Reflector->createFunctionEntity() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:94
    0.0608    2196640  12. PHPDocsMD\Reflector->getParams() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:135
    0.0608    2197624  13. PHPDocsMD\Reflector->createParameterEntity() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:412
    0.0608    2197624  14. PHPDocsMD\Reflector::getParamType() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:226
    0.0608    2198000  15. ReflectionParameter::export() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:318
    0.0608    2198184  16. Reflection::export() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:318
    0.0608    2198224  17. ReflectionParameter->__toString() /app/vendor/victorjonsson/markdowndocs/src/PHPDocsMD/Reflector.php:318

