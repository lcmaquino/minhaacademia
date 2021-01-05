## Descrição

A Minha Academia é uma aplicação web para gerenciamento de cursos livres online.

Para efetuar login na aplicação o usuário deve estar inscrito em um canal do
YouTube especificado nas configurações da aplicação.

## Instalação Local

### Pré-requisitos
Pré-requisitos para instalar a Minha Academia em um computador local:
- [XAMPP 7.x](https://www.apachefriends.org/pt_br/index.html);
- [Composer](https://getcomposer.org/);

Após instalar o XAMPP crie um link simbólico para o `php` com o comando:
```
$ sudo ln -s /opt/lampp/bin/php /usr/local/bin/php
```

**Observação**: por padrão, `/opt/lampp` é o diretório onde o XAMPP é instalado.

Usando o instalador `composer-setup.php` do Composer forneça os parâmetros
`--install-dir` e `--filename`:
```
$ php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

Agora você pode rodar os comandos `php` e `composer` estando em qualquer
diretório do seu sistema.

### Processo de instalação.

[Baixe os arquivos](https://github.com/lcmaquino/minhaacademia/archive/main.zip) do repositório e coloque no diretório `/opt/lampp`. Você
deve ficar com uma estrutura de diretórios contendo algo como:
```
/opt/lampp
| - minhaacademia
| - htdocs
|   | - site
| ...
```

Abra um terminal e execute os comandos abaixo. O grupo `daemon` é utilizado
pelo XAMPP instalado no Ubuntu. Seu sistema pode usar outro grupo (como, por exemplo, 
`www-data`).
```
$ cd /opt/lampp
$ sudo chown -R $USER:daemon minhaacademia
$ sudo chown -R $USER:daemon htdocs/site
$ cd minhaacademia
$ composer install
```

Aguarde o Composer baixar e instalar todas as dependências.

Crie um link simbólico para o diretório
`/opt/lampp/minhaacademia/storage/app/public`.
```
$ ln -s /opt/lampp/minhaacademia/storage/app/public /opt/lampp/htdocs/site/storage
```

Antes de começar a modificar sua aplicação eu recomendo alterar as permisões
dos diretórios e arquivos.
```
$ cd /opt/lampp
$ find minhaacademia -type d -exec chmod 755 {} \;
$ chmod -R g+w minhaacademia/storage
$ chmod -R g+w minhaacademia/bootstrap/cache
$ find minhaacademia -type f -exec chmod 644 {} \;
$ find htdocs/site -type d -exec chmod 775 {} \;
$ find htdocs/site -type f -exec chmod 644 {} \;
```

Dentro do diretório `/opt/lampp/minhaacademia` crie o arquivo `.env` para
configurar o ambiente de sua aplicação. O arquivo
`/opt/lampp/minhaacademia/.env.example` serve como modelo. Além disso,
gere uma chave de criptografia para a sua aplicação.
```
$ cd /opt/lampp/minhaacademia
$ cp .env.example .env
$ php artisan key:generate
```

Abra o phpMyAdmin digitando no seu navegador o endereço 
`http://localhost/phpmyadmin`. Crie um banco de dados `minhaacademia`
com codificação de caracteres (*collation*) `utf8_general_ci`. 
Conceda todas as permissões desse banco para o usuário `USUARIO`
com senha `SENHA`. Em seguida, modifique os seguintes parâmetros no arquivo 
`/opt/lampp/minhaacademia/.env` para configurar a autenticação no banco de
dados.
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=minhaacademia
DB_USERNAME=USUARIO
DB_PASSWORD=SENHA
```

Execute os comandos abaixo.
```
$ cd /opt/lampp/minhaacademia
$ php artisan migrate:fresh && php artisan db:seed
```

Nesse momento você pode testar a página inicial da Minha Academia 
no endereço `http://localhost/site`.

Acesse o endereço `http://localhost/site/login-panel` para efetuar o primeiro
login usando o usuário `admin@localhost` e senha `admin`.

**ALTERE** o e-mail e a senha do usuário `admin@localhost` clicando no ícone de
engrenagem no canto superior direito. Use o mesmo e-mail de sua conta de acesso
ao YouTube para que seu usuário seja o administrador da aplicação. 
Após efetuar essa alteração, clique no menu lateral `Configurações`
para inserir os dados de sua aplicação e de seu canal no YouTube.

Para que o usuário possa efetuar o login na sua aplicação usando a conta dele no
YouTube, o Google **EXIGE** que seja configurado um projeto de aplicação web 
usando credenciais de OAuth 2.0. Crie um projeto para sua aplicação em 
https://console.developers.google.com/ e ative a "YouTube Data API v3". Na
"Tela de consentimento OAuth" use os escopos `userinfo.email`, `openid` e
`youtube.readonly`.

**Observação**: devido ao uso do escopo `youtube.readonly` a sua aplicação
terá que passar por um processo de verificação por um time do Google. Enquanto a
sua aplicação não passar pela verificação, o usuário verá uma tela chamada
"App não verificado" durante o processo de login. Só é necessário submeter sua
aplicação para a verificação depois que você concluir todas as alterações que
desejar fazer na mesma.

## Instalação em Servidor de Hospedagem

Se o seu servidor de hospedagem fornece acesso SSH ou um terminal virtual
você pode basicamente replicar no servidor os procedimentos explicados
na **Instalação Local** modificando os diretórios conforme necessário. A sua
estrutura de diretórios deve ficar contendo algo como:
```
/home/USUARIO
| - minhaacademia
| - public_html
|   | - site
| ...
```

Configure o diretório público de seu servidor usando o parâmetro `PUBLIC_HTML`
no arquivo `/home/USUARIO/minhaacademia/.env`.
```
PUBLIC_HTML=/../public_html/site
```

Altere também esse diretório público no arquivo
`/home/USUARIO/minhaacademia/webpack.mix.js` modificando a seguinte linha.
```
const public_html = '../public_html/site';
```

Crie um link simbólico para o diretório
`/home/USUARIO/minhaacademia/storage/app/public`.
```
$ ln -s /home/USUARIO/minhaacademia/storage/app/public /home/USUARIO/public_html/site/storage
```

Coloque sua aplicação em ambiente de produção e desative as mensagens de *debug*
usando os parâmetros `APP_ENV` e `APP_DEBUG` no arquivo
`/home/USUARIO/minhaacademia/.env`.
```
APP_ENV=production
APP_DEBUG=false
```

Se o seu servidor de hospedagem não fornece acesso SSH e nem
um terminal virtual você pode fazer uma **Instalação Local** no seu computador
e depois copiar os arquivos para o servidor. Nessa situação você ainda terá que
configurar o diretório público do servidor conforme explicado acima. Além disso,
você precisa criar um link simbólico para o diretório 
`/home/USUARIO/minhaacademia/storage/app/public`. Nesse caso, você pode criar
um arquivo `link-simbolico.php` no diretório público de seu servidor e usar o
código abaixo.
```
<?php
  $target = '/home/USUARIO/minhaacademia/storage/app/public';
  $link = '/home/USUARIO/public_html/site/storage';
  symlink($target, $link);
```

Execute esse código acessando seudominio.com.br/link-simbolico.php. Se ele funcionar,
então você verá uma página em branco e o diretório `/home/USUARIO/public_html/site/storage`
foi criado no seu servidor. Caso contrário, serão exibidas mensagens de erro na página.
Depois que o link estiver criado você pode remover o arquivo `link-simbolico.php` do 
diretório público.

Por fim, se você quiser habilitar o reCAPTCHA v3 no seu formulário de contato para evitar
spam, acesse o endereço https://www.google.com/recaptcha/ e no console
administrativo crie as chaves. Depois insira essas chaves na página de 
`Configurações` da sua aplicação.