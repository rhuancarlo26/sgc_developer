<style>
  body {
    padding: 2em !important;
  }
    h3 {    
        border: 1px solid;
        border-radius: 5px;
        padding: 0.5em 1em;
    }

    h4 {    
        border-bottom: 1px solid;
        padding-bottom: 0.5em;
    }
</style>

# API ROUTES

<hr>

## Endpoints

| HTTP Verb | Endpoint                   |
|-----------|----------------------------|
| POST      | [/auth_token](#auth_token) |
| GET       | [/profile](#profile)       |

<hr> 

<h3 id="auth_token"> POST: /auth_token </h3>

Endpoint responsável por autenticar o usuário, e retornar o token de acesso necessário para requisições futuras.

#### Request:

##### Headers

| Param  | Value            | Required |
|--------|------------------|----------|
| Accept | application/json | true     |

##### Body

| Param    | Type   | Required |
|----------|--------|----------|
| email    | String | true     |
| password | String | true     |

Exemplo:

```json
{
    "email": "teste@teste.com",
    "password": "teste"
}
```

#### Response:

##### 200 Ok

Retornado quando a autenticação é realizada com sucesso.

```json
{
    "message": "Usuário Autenticado com sucesso",
    "token": "7|36bYxY4XXrl19KtZbh6jhnzA5v6F5RDx2L9zpbBF",
    "expires_at": "2000-00-00 00:00"
}
```

O token retornado deverá ser informado no Header 'Authorization' das próximas requisições após a string 'Bearer'.

Ex:

```
Authorization: Bearer 1|sbDCce9cTrRrkG7X2UgRbdBOM8xpBakw0aJ9Cf9y
```

O Token é válido por 7 dias (1440 minutos). Sendo necessária uma nova autenticação para gerar um novo token após este
período.

##### 422 Unprocessable Content

Retornado quando algum dos parametros obrigatórios não foram informados.

```json
{
    "message": "O campo X é obrigatório."
}
```

##### 401 Unauthorized

Retornado quando não foi possível realizar autenticação com email e senha informados.

```json
{
    "message": "Credenciais Inválidas"
}
```

##### 500 Server Error

Retornado quando houver algum erro no servidor durante a requisição.

<hr>

<h3 id="user"> GET /profile </h3>
Retorna os dados do usuário conforme o Bearer Token

#### Request:

##### Headers

| Param         | Value            | Required |
|---------------|------------------|----------|
| Accept        | application/json | true     |
| Authorization | Bearer {token}   | true     |

#### Response:

##### 200 Ok

Retornado quando a requisição foi realizada com sucesso.

```json
{
    "id": 1,
    "name": "Sample User",
    "email": "user@sample.com",
    "email_verified_at": "2023-11-11T20:35:49.000000Z",
    "created_at": "2023-11-11T20:35:49.000000Z",
    "updated_at": "2023-11-11T20:35:49.000000Z",
    "roles": [
        {
            "id": 1,
            "name": "Super Admin",
            "guard_name": "web",
            "created_at": "2023-11-11T20:35:29.000000Z",
            "updated_at": "2023-11-11T20:35:29.000000Z",
            "pivot": {
                "model_type": "App\\Models\\User",
                "model_id": 1,
                "role_id": 1
            }
        }
    ],
    "permissions": []
}

```
