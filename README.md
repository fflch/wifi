# Sistema de Acesso Wi-Fi para Visitantes — FFLCH-USP

[![Laravel](https://img.shields.io/badge/Laravel-13-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5-blue)](https://php.net)
[![MariaDB](https://img.shields.io/badge/MariaDB-11-orange)](https://mariadb.org)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

Sistema web para gerenciamento de acesso à rede Wi-Fi de visitantes da FFLCH-USP. Visitantes cadastram o MAC do dispositivo e recebem acesso temporário após aprovação de um patrocinador (funcionário/docente).

---

## Funcionalidades

- **Cadastro de visitantes** com nome, e-mail, CPF, telefone e endereço MAC
- **Validação e normalização** automática de MAC (`aa:bb:cc:dd:ee:ff`)
- **Redirecionamento inteligente** por estado do MAC (novo / pendente / aprovado / expirado)
- **Aprovação/rejeição** por patrocinadores autenticados via Senha Única USP
- **Consulta pública** de solicitação por protocolo ULID
- **API JSON** para controladora Wi-Fi consultar MACs liberados
- **Expiração automática** de acessos (agendado a cada 5 minutos)
- **Auditoria** de todas as alterações de status
- **Rate limiting** contra abuso do formulário
- **Painel do patrocinador** com estatísticas e busca (nome, e-mail, CPF, MAC)

---

## Stack

| Componente | Tecnologia |
|---|---|
| Framework | Laravel 13 |
| PHP | 8.5 |
| Banco | MariaDB 11 (produção) / SQLite (testes) |
| Autenticação | Senha Única USP (`uspdev/senhaunica-socialite`) |
| Tema | `laravel-usp-theme` |
| Container | Docker + Docker Compose |
| Servidor | Apache 2.4 + PHP-FPM |
| Testes | PHPUnit + Laravel Dusk |
| IDs | ULID (anti-IDOR) |

---

## Requisitos

- Docker + Docker Compose
- Portas 8000, 3306, 3141, 7900 livres

---

## Instalação Rápida

```bash
# 1. Configure o ambiente
cp .env.example .env
# Edite .env conforme necessário (ver seção abaixo)

# 2. Suba os containers
docker compose up --build

# 3. criar key
docker exec wifi php artisan key:generate

# 3. Rode as migrations
docker exec wifi php artisan migrate

Exemplo de URL de teste: http://127.0.0.1:8000/solicitar?client_mac=d8:84:8c:10:88:10

```

Acesse: [http://127.0.0.1:8000](http://127.0.0.1:8000)

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
| `SENHAUNICA_ADMINS` | Codpes dos patrocinadores (separados por `;`) | `16001;16002` |
| `SENHAUNICA_CALLBACK` | Callback OAuth | `http://127.0.0.1:8000/callback` |
| `WIFI_CONTROLLER_TOKEN` | Token da API da controladora | *(configurar em produção)* |
| `WIFI_ACCESS_VALIDITY_HOURS` | Validade padrão do acesso (horas) | `12` |

---

## Uso

### Visitante

1. Conecte-se à rede Wi-Fi da FFLCH
2. O captive portal redireciona para `/solicitar?client_mac=XX:XX:XX:XX:XX:XX`
3. Preencha o formulário com nome, e-mail, CPF, telefone
4. Receba o protocolo ULID para acompanhamento
5. Aguarde a aprovação do patrocinador
6. Após aprovado, o acesso é liberado automaticamente

### Patrocinador

1. Acesse `/login` e autentique com Senha Única USP
2. Veja as pendências em `/aprovacoes`
3. Aprove (com validade em horas) ou rejeite solicitações
4. Acompanhe estatísticas no `/dashboard`

### API (Controladora Wi-Fi)

```
GET /api/wifi/aprovados
Header: X-Controller-Token: <token>

GET /api/wifi/mac/{mac}
Header: X-Controller-Token: <token>

GET /api/wifi/fila
Header: X-Controller-Token: <token>
```

---

## Comandos Úteis

```bash
# Expirar acessos manualmente
docker exec wifi-app php artisan wifi:expirar

# Listar schedules ativos
docker exec wifi-app php artisan schedule:list

# Rodar tests (Feature + Unit, SQLite)
docker exec wifi-app sh -c 'DB_CONNECTION=sqlite DB_DATABASE=":memory:" php artisan test'

# Rodar Lint (Pint)
docker exec wifi-app php vendor/bin/pint --test
```

---

## Testes

- **15 testes de Feature:** cobrem validação de MAC, criação/expiração de solicitações, API endpoints (401, 404, 400, 200, 503)
- **5 testes Dusk:** navegação real via Selenium (solicitar, aprovar, rejeitar, dashboard, bloqueio)

---

## Estrutura do Projeto

```
app/
├── Console/Commands/ExpirarAcessosWifi.php   # Comando de expiração
├── Enums/WifiRequestStatus.php                # Enum de estados
├── Http/
│   ├── Controllers/
│   │   ├── VisitanteWifiController.php        # Fluxo do visitante
│   │   ├── PatrocinadorWifiController.php     # Painel do patrocinador
│   │   └── WifiStatusController.php           # API da controladora
│   ├── Middleware/
│   │   ├── CheckPatrocinador.php              # Gate de patrocinador
│   │   ├── ControllerTokenAuth.php            # Auth da API
│   │   ├── LogoutUnauthorized.php             # Desloga não-autorizados
│   │   └── SecureHeadersMiddleware.php        # Headers de segurança
│   └── Requests/SolicitarWifiRequest.php      # Validação do formulário
├── Models/
│   ├── Visitor.php                            # Visitante
│   ├── WifiRequest.php                        # Solicitação de acesso
│   ├── User.php                               # Usuário (patrocinador)
│   └── AuditLog.php                           # Log de auditoria
├── Observers/WifiRequestObserver.php          # Auditoria automática
├── Policies/WifiRequestPolicy.php             # Policy de gerenciamento
├── Services/WifiRequestService.php            # Lógica de negócio
└── Support/MacAddress.php                     # Helper de MAC

database/migrations/                           # Migrations do banco
resources/views/wifi/                          # Views do sistema
routes/web.php                                 # Rotas web
routes/console.php                             # Schedule
config/wifi.php                                # Configuração do módulo
tests/Feature/                                 # Testes de feature
tests/Browser/                                 # Testes Dusk
```

---

## Estados da Solicitação

```
pending ──▶ approved ──▶ expired
  │
  └──▶ rejected
```

- **pending:** aguardando aprovação do patrocinador
- **approved:** acesso liberado por tempo determinado (expires_at)
- **rejected:** patrocinador negou o acesso
- **expired:** prazo de validade expirou (transição automática)

---

## Licença

MIT — Projeto desenvolvido pela STI-FFLCH-USP.