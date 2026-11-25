{{--
    Layout das telas do visitante (captive portal).

    Antes da liberação do MAC a controladora só deixa o dispositivo acessar
    este servidor (walled garden). Por isso esta página NÃO pode depender de
    nada externo: sem laravel-usp-theme, CDN, Google Fonts, Font Awesome ou
    jQuery. CSS e JS ficam inline; imagens só de public/ deste servidor.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Wi-Fi Visitantes') - FFLCH-USP</title>
    <link rel="icon" href="/favicon.ico">
    <style>
        :root {
            --azul: #273e74;
            --azul-claro: #e9edf6;
            --verde: #1e7e34;
            --amarelo: #d39e00;
            --vermelho: #c82333;
            --cinza: #6c757d;
            --borda: #dee2e6;
            --fundo: #f4f6f9;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #212529;
            background: var(--fundo);
        }
        header { background: #fff; border-bottom: 4px solid var(--azul); padding: 12px 16px; }
        header .wrap { display: flex; align-items: center; gap: 16px; }
        header img { height: 48px; width: auto; }
        header span { color: var(--azul); font-weight: 700; font-size: 1.1rem; border-left: 1px solid var(--borda); padding-left: 16px; }
        .wrap { max-width: 640px; margin: 0 auto; }
        main { padding: 24px 16px 48px; }
        footer { text-align: center; color: var(--cinza); font-size: .8rem; padding: 0 16px 24px; }

        h1 { font-size: 1.5rem; margin: 0 0 8px; }
        h2 { font-size: 1rem; margin: 0 0 16px; color: var(--azul); text-transform: uppercase; }
        p { margin: 0 0 12px; }
        .muted { color: var(--cinza); }
        .small { font-size: .875rem; }
        .center { text-align: center; }
        .text-success { color: var(--verde); }
        .text-danger { color: var(--vermelho); }

        .card { background: #fff; border: 1px solid var(--borda); border-radius: 8px; margin-bottom: 16px; }
        .card-body { padding: 24px; }
        .card-body + .card-body { border-top: 1px solid var(--borda); }
        .card-footer { padding: 16px 24px; background: #f8f9fa; border-top: 1px solid var(--borda); border-radius: 0 0 8px 8px; }

        .icon {
            width: 80px; height: 80px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2.2rem; line-height: 1; margin-bottom: 16px;
        }
        .bg-success { background: var(--verde); }
        .bg-warning { background: var(--amarelo); }
        .bg-danger { background: var(--vermelho); }

        .info { display: inline-block; background: var(--azul-claro); border-radius: 6px; padding: 12px 16px; margin: 8px 0 16px; max-width: 100%; }
        .info small { display: block; text-transform: uppercase; font-weight: 700; font-size: .75rem; color: var(--cinza); }
        .info strong { color: var(--azul); word-break: break-all; }
        .mono { font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 1.1rem; }

        ul.lista { text-align: left; color: var(--cinza); padding-left: 20px; }

        label { display: block; font-weight: 600; font-size: .85rem; margin-bottom: 4px; }
        .field { margin-bottom: 16px; }
        .row { display: flex; gap: 16px; flex-wrap: wrap; }
        .row > .field { flex: 1 1 220px; }
        input[type=text], input[type=email], textarea {
            width: 100%; padding: 10px 12px; font: inherit;
            border: 1px solid #ced4da; border-radius: 6px; background: #fff;
        }
        input:focus, textarea:focus { outline: 2px solid var(--azul); outline-offset: 0; border-color: var(--azul); }
        .hint { display: block; color: var(--cinza); font-size: .8rem; margin-top: 4px; }
        .req { color: var(--vermelho); }
        .step { display: inline-block; background: var(--azul); color: #fff; border-radius: 4px; padding: 0 8px; margin-right: 8px; }

        .btn {
            display: inline-block; padding: 10px 20px; border-radius: 6px; font: inherit; font-weight: 600;
            text-decoration: none; cursor: pointer; border: 2px solid var(--azul);
            background: var(--azul); color: #fff;
        }
        .btn:disabled { opacity: .6; cursor: wait; }
        .btn-outline { background: #fff; color: var(--azul); }
        .footer-flex { display: flex; gap: 16px; align-items: center; justify-content: space-between; flex-wrap: wrap; }

        .alert { background: #f8d7da; border: 1px solid #f5c2c7; color: #842029; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; }
        .alert-info { background: #fff3cd; border-color: #ffe69c; color: #664d03; }
        .alert ul { margin: 4px 0 0; padding-left: 20px; }
        .vinculo { background: #e9edf6; border-left: 4px solid var(--azul); border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; font-size: .9rem; }
    </style>
</head>
<body>
    <header>
        <div class="wrap">
            <img src="/images/logo_fflch.png" alt="FFLCH-USP">
            <span>Wi-Fi Visitantes</span>
        </div>
    </header>

    <main>
        <div class="wrap">
            <div class="vinculo">
                Esta rede é somente para <strong>visitantes sem vínculo com a Universidade de São Paulo</strong>.
                Alunos, docentes, funcionários e demais pessoas com vínculo USP devem usar a rede <strong>eduroam</strong>.
            </div>

            @yield('content')
        </div>
    </main>

    <footer>
        Faculdade de Filosofia, Letras e Ciências Humanas &middot; Universidade de São Paulo
    </footer>

    @yield('scripts')
</body>
</html>
