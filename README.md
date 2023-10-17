# PROJETO CG - STORE API

O "PROJETO CG - STORE API" tem como principal objetivo o desenvolvimento do back-end de uma aplicação projetada para gerenciar o estoque de produtos e facilitar as operações de vendas.

## Instrucoes

### Pré-requisitos

Requisitos para o software e outras ferramentas necessárias para construir, testar e implantar:

-   **Banco de Dados**: [MySQL](https://www.mysql.com/)
-   **BACKEND**:
    -   **Linguagem**: [PHP](https://www.php.net/)
    -   **Gerenciador de Pacotes**: [Composer](https://getcomposer.org/)
    -   **Framework**: [Laravel](https://laravel.com/docs/10.x/installation)

### Executando a Aplicação

Com o banco de dados MySQL em execução, alterar o arquivo .env linha 14 (DB_DATABASE=DB_TESTE_CG).
(Altere para o nome desejado no seu banco de dados.)

Acesse a pasta principal da aplicação e execute o seguinte comando no terminal:

```shell
    php artisan migrate && php artisan serve
```

Em seguida, uma mensagem indicará em qual porta o projeto está sendo executado.
Você pode usar o seu cliente preferido (Insomnia, Postman, ThunderClient) para acessar as rotas da API.

### Endpoints:

#### Produtos:

<details>
<summary>Clique para exibir os Endpoints dos Produtos</summary>

-   **GET** [http://localhost:8000/products](http://localhost:8000/products)
    -   (Lista todos os produtos)
-   **GET** [http://localhost:8000/products/:id](http://localhost:8000/products/:id)
    -   (Retorna produto pelo id)
-   **POST** [http://localhost:8000/products](http://localhost:8000/products)
    -   (Cadastra um produto)
        -   **Exemplo de corpo da requisição em formato JSON**:
        ```json
        {
            "name": "nome do produto",
            "description": "descrição do produto",
            "price": 1.99,
            "stock": 100
        }
        ```
-   **PATCH** [http://localhost:8000/products/:id](http://localhost:8000/products/:id)
    -   (Atualiza um produto)
-   **DELETE** [http://localhost:8000/products/:id](http://localhost:8000/products/:id)
    -   (Deleta um produto)

</details>

#### Vendas:

<details>
<summary>Clique para exibir os Endpoints das Vendas</summary>

-   **GET** [http://localhost:8000/sales](http://localhost:8000/sales)
    -   (Histórico de Vendas)
-   **POST** [http://localhost:8000/sales](http://localhost:8000/sales)
    -   (Cria uma venda)
    -   **Exemplo de corpo em formato JSON**:
    ```json
    {
        "product_id": 1,
        "quantity": 5
    }
    ```
-   **PATCH** [http://localhost:8000/sales/:id](http://localhost:8000/sales/:id)
    -   (Atualiza uma venda)
    -   **Exemplo de corpo em formato JSON**:
    ```json
    {
        "product_id": 1,
        "quantity": 5
    }
    ```
-   **PATCH** [http://localhost:8000/sales/:id/cancel](http://localhost:8000/sales/:id/cancel) - (Cencela uma venda) - **Exemplo de corpo em formato JSON**:
`json
    {
        "status": "canceled"
    }
    `
</details>

## Descrição do Projeto

<details>
<summary>Clique para exibir os Requisitos do Projeto</summary>

<details>
<summary>REQUISITO 1</summary>

Criar um endpoint que permita o cadastro de um novo produto com os campos: name, description, price e stock. Nome, descrição, preço e quantidade em estoque, respectivamente.

-   **POST** [http://localhost:8000/products](http://localhost:8000/products) - (Cadastra um produto) - **Exemplo de corpo da requisição em formato JSON**:
```json
    {
        "name": "nome do produto",
        "description": "descrição do produto",
        "price": 1.99,
        "stock": 100
    }
```
</details>

<details>
<summary>REQUISITO 2</summary>

Implementar um endpoint para listar todos os produtos disponíveis no estoque.

-   **GET** [http://localhost:8000/products](http://localhost:8000/products) - (Lista todos os produtos)
</details>

<details>
<summary>REQUISITO 3</summary>

Desenvolver um endpoint para obter os detalhes de um produto através do ID.

-   **GET** [http://localhost:8000/products/:id](http://localhost:8000/products/:id)
    -   (Retorna produto pelo id)
</details>

<details>
<summary>REQUISITO 4</summary>

Criar um endpoint para atualizar as informações de um produto através do ID.

-   **PATCH** [http://localhost:8000/products/:id](http://localhost:8000/products/:id)
    -   (Atualiza um produto, pode-se passar os campos completos, como no exemplo, ou apenas algumas colunas)
```json
    {
        "name": "nome do produto",
        "description": "descrição do produto",
        "price": 1.99,
        "stock": 100
    }
```
</details>

<details>
<summary>REQUISITO 5</summary>

Implementar um endpoint para excluir um produto através do ID.

-   **DELETE** [http://localhost:8000/products/:id](http://localhost:8000/products/:id)
    -   (Deleta um produto)
</details>

<details>
<summary>REQUISITO 6</summary>

Adicionar um novo endpoint que permita realizar uma venda, onde o produto é selecionado e a quantidade vendida é registrada. Isso deve reduzir a quantidade de estoque do produto.

-   **POST** [http://localhost:8000/sales](http://localhost:8000/sales)
    -   (Cria uma venda)
    -   **Exemplo de corpo em formato JSON**:
    ```json
    {
        "products": [
        { "product_id": 1, "quantity": 5 },
        { "product_id": 2, "quantity": 3 },
        // Outros produtos
    ]
    }

    ```
</details>

<details>
<summary>REQUISITO 7</summary>

Garantir que a quantidade em estoque seja atualizada automaticamente quando novas unidades são vendidas.(complemento do requisito anterior)

</details>


<details>
<summary>REQUISITO 8</summary>

Adicionar um novo endpoint que permita cancelar uma venda, onde o produto é selecionado e a quantidade vendida é registrada. Isso deve aumentar a quantidade de estoque do produto.

-   **PATCH** [http://localhost:8000/sales/:id/cancel](http://localhost:8000/sales/:id/cancel)
    - (Cencela uma venda)
    - **Exemplo de corpo em formato JSON**:
    ```json
        {
            "status": "canceled"
        }
    ```
</details>

<details>
<summary>REQUISITO 9</summary>

Adicionar um novo endpoint que permita editar uma venda, onde o produto é selecionado e a quantidade vendida é registrada. Isso deve reduzir ou aumentar a quantidade em estoque do produto dependendo da edição.

-   **PATCH** [http://localhost:8000/sales/:id](http://localhost:8000/sales/:id)
    -   (Atualiza uma venda)
    -   **Exemplo de corpo em formato JSON**:
    ```json
        {
            "product_id": 1,
            "quantity": 5
        }
    ```
</details>

<details>
<summary>REQUISITO 10</summary>

Criar um endpoint para listar o histórico de vendas, incluindo informações sobre os produtos vendidos, quantidades e data da venda.

-   **GET** [http://localhost:8000/sales/history](http://localhost:8000/sales/history)
    -   (Histórico de Vendas)

</details>

</details>

## Autor

-   **Iago Gois** - _Web Developer_
    [dev-iago-gois](https://github.com/dev-iago-gois)

## License

Este projeto está licenciado sob a [Licença Universal CC0 1.0](LICENSE.md) da Creative Commons
consulte o arquivo [LICENSE.md](LICENSE.md) para mais detalhes.


TODO:
- Aplicar conceito de Repository
- Aplicar conceito de DB transaction
- Applicar try catch
- refatorar o codigo e substituir os status code pelo do laravel ex: Response::HTTP_BAD_REQUEST
use Illuminate\Http\Response;
