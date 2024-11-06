Gerar arquivo PHPUnit(vendor/bin/phpunit --generate-configuration)

testdox="true"
colors="true"

#{CoversClass('')} -> Para anotar qual classe está sendo coberta.

Ir no compose.json e definir o autoload (responsável por carregar as classes do projeto)

"autoload": {
    "psr-4": {
        "application"
    }
}

#DataProvider() -> Fazer a função de teste receber algum parâmetro

"coverage": "XDEBUG_MODE=coverage phpunit --coverage-html coverage"