# system-admin
Projeto base do Adianti Template 7.4.1

## Requisitos
- Docker PHP 8.1 + Apache
- Docker MySQL 8
 
## Modificações
- Hash de senha de MD5 para Argon 2
- <code>/src/app/database/fix.sql</code> no entrypoint de MySQL pra criar tabela faltante <code>system_access_notification_log.</code>
- Migration e Seeder com PHP Phinx
- Filtro de itens de menu
