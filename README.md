# Sistema de Acesso Wi-Fi para Visitantes — FFLCH-USP


Sistema web para gerenciamento de acesso à rede Wi-Fi de visitantes da FFLCH-USP. Visitantes cadastram o MAC do dispositivo e recebem acesso após aprovação de um autorizador (funcionário/docente).

---

## Funcionalidades

- **Cadastro de visitantes** com nome, e-mail, CPF, telefone e endereço MAC
- **Redirecionamento inteligente** por estado do MAC (novo / pendente / aprovado / rejeitado)
- **Aprovação/rejeição** por servidores (vínculo `Servidor` na Senha Única USP)
- **Liberação automática no FreeRADIUS** (tabela `radcheck`)
- **Histórico de status** (spatie/laravel-model-status) e **auditoria** (owen-it/laravel-auditing)

---

## Instalação Rápida

```bash
# 1. Configure o ambiente
cp .env.example .env
# Edite .env conforme necessário (ver seção abaixo)

# 2. Suba os containers
docker compose up --build

# 3. Instale as dependências (o volume ./ esconde o vendor/ da imagem)
docker exec wifi composer install

# 4. Crie a key
docker exec wifi php artisan key:generate

# 5. Rode as migrations (cria também a tabela radcheck do FreeRADIUS)
docker exec wifi php artisan migrate
```

Exemplo de URL de teste: 

    http://127.0.0.1:8000/solicitar?client_mac=d8:84:8c:10:88:10&NbiIP=10.10.10.10&ssid=fflch

Acesse: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## FreeRADIUS (MAC Authentication)

A controladora Wi-Fi pergunta ao FreeRADIUS se um MAC pode entrar na rede.
O FreeRADIUS responde consultando a tabela **`radcheck`**, que é mantida **somente pelo Laravel**:

```
Controladora ──Access-Request (MAC)──▶ FreeRADIUS ──SELECT──▶ radcheck ◀──INSERT/DELETE── Laravel
            ◀──Access-Accept/Reject──
```

| Evento no Laravel | Efeito na `radcheck` |
|---|---|
| Solicitação aprovada | insere `username=aabbccddeeff, attribute=Auth-Type, op=:=, value=Accept` |
| Rejeitada / revogada | remove a linha do MAC |
| `php artisan app:sincroniza-radius` | refaz a tabela inteira (uso manual, ex.: após restaurar o banco) |

- A sincronização é automática: toda mudança de status dispara `Visitante::sincronizarRadius()` (ver `AppServiceProvider`).
- O MAC é aceito em qualquer formato (`AA-BB-CC-DD-EE-FF`, `aa:bb:cc:dd:ee:ff`, `aabb.ccdd.eeff`, `AABBCCDDEEFF`),
  vindo em `User-Name` ou, se este não for um MAC, em `Calling-Station-Id`. A senha é ignorada.
- Configuração em [freeradius/](freeradius/): `clients.conf` (NAS e secret), `mods-enabled/sql` (conexão e query), `sites-enabled/default` (lógica).
- O secret vem da variável `RADIUS_SECRET` (padrão `MinhaChaveSecretRADIUS`).

### Simulando a controladora

`radtest` e `radclient` já estão dentro do container `wifi_freeradius`.
Sintaxe do radtest: `radtest -t pap <usuario> <senha> <servidor> <nas-port> <secret>`.

```bash
# MAC liberado  -> "Received Access-Accept"
# MAC não liberado -> "Received Access-Reject" + Reply-Message = "MAC nao autorizado"
docker exec wifi_freeradius radtest -t pap C4-D0-E3-71-97-6A C4-D0-E3-71-97-6A 127.0.0.1 0 MinhaChaveSecretRADIUS

# Requisição parecida com a que a Ruckus envia (MAC no Calling-Station-Id)
printf 'User-Name = "C4D0E371976A"\nUser-Password = "C4D0E371976A"\nCalling-Station-Id = "C4-D0-E3-71-97-6A"\nCalled-Station-Id = "AA-BB-CC-00-11-22:FFLCH-Visitantes"\nNAS-IP-Address = 10.0.0.1\n' \
  | docker exec -i wifi_freeradius radclient -x 127.0.0.1:1812 auth MinhaChaveSecretRADIUS

# Ver o que está liberado
docker exec wifi_mariadb mariadb -uwifi -pwifi wifi -e 'SELECT * FROM radcheck'

# Ver cada requisição sendo processada (o container roda em modo debug -X)
docker logs -f wifi_freeradius
```

Teste completo: abra `http://127.0.0.1:8000/solicitar?client_mac=C4:D0:E3:71:97:6A&NbiIP=10.10.10.10&ssid=fflch`, rode o `radtest` (Reject),
aprove em `/aprovacoes`, rode de novo (Accept), revogue e rode mais uma vez (Reject).

> Use `127.0.0.1` e não `localhost` no radtest: `localhost` pode resolver para IPv6 (`::1`), que não está em `clients.conf`.
> Secret errado não gera Reject: o servidor descarta o pacote e o radtest mostra `No reply from server`.

**Produção:** em `freeradius/clients.conf` troque `0.0.0.0/0` pelo IP da vSZ, defina um `RADIUS_SECRET` forte,
remova o `command: ["-X"]` do `docker-compose.yml` e, de preferência, use um usuário MariaDB só com `SELECT` em `radcheck`.

---

## Configuração (.env)

| Variável | Descrição | Exemplo |
|---|---|---|
| `APP_URL` | URL base | `http://127.0.0.1:8000` |
| `DB_HOST` | Host do MariaDB | `wifi-db` |
| `DB_DATABASE` | Nome do banco | `cursolaravel` |
| `DB_USERNAME` | Usuário do banco | `cursolaravel` |
| `DB_PASSWORD` | Senha do banco | `cursolaravel` |
| `SENHAUNICA_KEY` | Consumer key OAuth | `fflch_sti` |
| `SENHAUNICA_SECRET` | Consumer secret OAuth | *(fornecido pela STI)* |
| `SENHAUNICA_ADMINS` | Codpes dos admins do senhaunica (separados por `,`) | `111111` |
| `SENHAUNICA_CODIGO_UNIDADE` | Unidade(s) cujo vínculo vira `Servidor`; de outras unidades vira `Servidorusp` | `8` |
| `SENHAUNICA_CALLBACK` | Callback OAuth | `http://127.0.0.1:8000/callback` |
| `WIFI_SSID` | SSID aceito no parâmetro `ssid` do `/solicitar` | `fflch` |
| `WIFI_NBI_IP` | IP da controladora aceito no parâmetro `NbiIP` do `/solicitar` | `10.10.10.10` |

---

## Uso

### Visitante

1. Conecte-se à rede Wi-Fi da FFLCH
2. O captive portal redireciona para `/solicitar?client_mac=XX:XX:XX:XX:XX:XX&NbiIP=<ip-controladora>&ssid=<ssid>`
3. Preencha o formulário com nome, e-mail, CPF/passaporte, telefone e motivo
4. Acompanhe em `/solicitar/{protocolo}` (o protocolo é um ULID)
5. Após aprovado, o acesso é liberado automaticamente
6. Se for rejeitado e voltar ao portal, a solicitação é reaberta para outro servidor

### Autorizador (vínculo `Servidor`)

1. Acesse `/login` e autentique com Senha Única USP
2. Aprove ou rejeite as pendências em `/aprovacoes` (o acesso não expira)
3. Em `/aprovacoes/minhas` veja o que você decidiu e revogue suas aprovações
4. Em `/aprovacoes/{protocolo}` veja os dados, o histórico de status e a auditoria

---

## Rotas

| Método | Rota | Controller | Quem |
|---|---|---|---|
| GET | `/solicitar` | `SolicitacaoController@create` | visitante |
| POST | `/solicitar` | `SolicitacaoController@store` | visitante |
| GET | `/solicitar/{solicitacao}` | `SolicitacaoController@show` | visitante |
| GET | `/aprovacoes` | `AprovacaoController@index` | servidor |
| GET | `/aprovacoes/minhas` | `AprovacaoController@minhas` | servidor |
| GET | `/aprovacoes/{solicitacao}` | `AprovacaoController@show` | servidor |
| PATCH | `/aprovacoes/{solicitacao}/aprovar` | `AprovacaoController@aprovar` | servidor |
| PATCH | `/aprovacoes/{solicitacao}/rejeitar` | `AprovacaoController@rejeitar` | servidor |

As rotas de servidor usam `Gate::authorize('senhaunica.servidor')`, gate criado pelo senhaunica-socialite.

---

## Comandos Úteis

```bash
# Refazer a tabela radcheck do FreeRADIUS
docker exec -it wifi php artisan app:sincroniza-radius

# Testes de Feature (SQLite em memória)
docker exec -it wifi sh -c 'DB_CONNECTION=sqlite DB_DATABASE=":memory:" php artisan test'

# Testes Dusk: troque no .env APP_URL=http://wifi e assista em http://localhost:7900 (senha: secret)
docker exec -it wifi php artisan dusk:chrome-driver
docker exec -it wifi php artisan dusk
```

---

## Estrutura do Projeto

```
app/
├── Console/Commands/SincronizaRadius.php      # app:sincroniza-radius
├── Http/
│   ├── Controllers/
│   │   ├── SolicitacaoController.php          # Visitante (captive portal)
│   │   └── AprovacaoController.php            # Servidor (aprovar/rejeitar)
│   ├── Middleware/SecureHeadersMiddleware.php # Headers de segurança
│   └── Requests/SolicitacaoRequest.php        # Validação do formulário
└── Models/
    ├── Visitante.php                          # Dispositivo/pessoa (MAC) + sincronizarRadius()
    ├── Solicitacao.php                        # Pedido de acesso (HasStatuses, Auditable)
    ├── RadCheck.php                           # Tabela do FreeRADIUS
    └── User.php                               # Usuário Senha Única

resources/views/
├── layouts/portal.blade.php                   # Layout do visitante (sem CDN)
├── solicitacoes/                              # create, show
└── aprovacoes/                                # index, minhas, show
```

---

## Estados da Solicitação

```
pendente ──▶ aprovado ──▶ rejeitado (revogado por quem aprovou)
   │
   └──▶ rejeitado ──▶ pendente (visitante volta ao portal)
```

Os status ficam na tabela `statuses` (spatie/laravel-model-status): `$solicitacao->setStatus('aprovado')`, `$solicitacao->status`.

---

## Licença

MIT — Projeto desenvolvido pela STI-FFLCH-USP.