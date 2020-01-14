# CSV WORKER

Utilitário para transformações entre arquvos .CSV

Essa ferramenta é útil quando você possui um arquivo .csv que precisa ser transformado, como por exemplo, mudança de posição de entre colunas, concatenação de várias colunas resultando em uma nova coluna, etc.

```
input.csv => config.json => output.csv
```

## Modo de usar

Crie um arquivo de configuração (por ex: `config.json`)
Este arquivo deverá conter as instruções que serão interpretadas pelo script.

Exemplo:

```json
{
  "input": "input.csv",
  "output": "output.csv",
  "skip_lines": 1,
  "mapping": [
    {
      "index": 0,
      "header": "Código de Integração",
      "maxlength": 20,
      "required": true,
      "src": 2,
      "padding": true
    },
    {
      "index": 1,
      "header": "Nome da Conta",
      "maxlength": 100,
      "required": true,
      "src": 1,
      "transform": "uppercase",
      "padding": true
    },
    {
      "index": 2,
      "header": "CPF_CNPJ",
      "maxlength": 18,
      "required": true,
      "src": 0,
      "transform": "only_numbers"
    },
    {
      "index": 3,
      "header": "Vendedor",
      "setvalue": "00000"
    }
  ]
}
```

### Parâmetros do arquivo `config.json`

- `input`: O arquivo com os dados a serem lidos

- `output`: O arquivo que será gravado após a transformação.

- `mappings`: Array com configuração de cada coluna

### Configuração de coluna

- `index`: índice da coluna que está sendo configurada
- `header`: Nome da coluna destino no cabeçalho
- `maxlength`: Tamanho máximo da string. Será usado `substr` caso necessário.
- `minlength`: Tamanho mínimo da string. Será usado `str_pad` caso necessário.
- `transform`: Opções de transformação entre origem e destino.
- etc.

Com o PHP instalado no path, execute o arquivo index.php passando como parâmetro o arquivo de configuração `.json` criado:

```shell
php index.php config.json
```
