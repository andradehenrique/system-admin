# system-admin
Projeto base do Adianti Template 7.5.1b

## Requisitos
- Docker PHP 8.2 FPM + Nginx
- Docker MySQL 8
 
## Modificações
- Hash de senha de MD5 para Argon 2
- <code>src/app/lib/validator/HPasswordValidator.class.php</code> pattern senha segura
- Migration e Seeder com PHP Phinx
- Filtro de itens de menu -> [Demonstração da funcionalidade](https://youtu.be/ZcbmnRd0coQ)
