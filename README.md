## Descrição

A Minha Academia é uma aplicação web para gerenciamento de cursos livres online.

Para efetuar login na aplicação o usuário deve estar inscrito em um canal do
YouTube especificado nas configurações da aplicação.

## Instalação local

### Pré-requisitos

Pré-requisitos para instalar a Minha Academia em um computador local:
- [XAMPP 7.3.x](https://www.apachefriends.org/pt_br/index.html);
- [Composer](https://getcomposer.org/);

**Observação**

Após instalar o XAMPP e o Composer certifique-se de que os comandos `php` e 
`composer` estão disponíveis no seu sistema.

No Linux isso por ser feito com os comandos:
```
sudo ln -s /opt/lampp/bin/php /usr/local/bin/php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

Aqui supondo que o XAMP está instalado em `/opt/lampp` (o padrão no Linux).

Já no Windows 10 o comando é (mude conforme sua versão do Windows):
```
setx PATH "%PATH%;C:\xampp\php" /M
```

Aqui supondo que o XAMP está instalado em `C:\xampp` (o padrão no Windows). Note
que esse comando apenas adiciona o caminho para a pasta de instalação do PHP.
O caminho do Composer já será adicionado à variável de ambiente caso você use
o instalador executável dele.

### Instalação no Linux

[Baixe os arquivos](https://github.com/lcmaquino/minhaacademia/archive/main.zip)
do repositório e coloque no diretório `/opt/lampp`. Você deve ficar com uma
estrutura de diretórios contendo algo como:
```
/opt/lampp
| - minhaacademia
| - htdocs
|   | - site
| ...
```

Antes de continuar a instalação você precisa ter permissões de leitura e
escrita nos diretórios `/opt/lampp/minhaacademia` e `/opt/lampp/htdocs/site`.

Mude suas permissões com os comandos no terminal:
```
cd /opt/lampp
sudo chown -R $USER:daemon minhaacademia
sudo chown -R $USER:daemon htdocs/site
```

Em seguida, use o Composer para baixar todos os arquivos necessários:
```
cd /opt/lampp/minhaacademia
composer install
```

Aguarde o Composer baixar e instalar todas as dependências.

Dentro do diretório `/opt/lampp/minhaacademia` crie o arquivo `.env` para
configurar o ambiente de sua aplicação. O arquivo
`/opt/lampp/minhaacademia/.env.example` serve como modelo.

Abra o phpMyAdmin digitando no seu navegador o endereço 
`http://localhost/phpmyadmin`. Crie um banco de dados `minhaacademia`
com codificação de caracteres (*collation*) `utf8_general_ci`. 
Conceda todas as permissões desse banco para o usuário `USUARIO`
com senha `SENHA` (trocando `USUARIO` e `SENHA` conforme sua preferência).
Em seguida, modifique os seguintes parâmetros no arquivo
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

Abra o seu navegador e digite o endereço `http://localhost/site/install.php`.
Escolha um e-mail e uma senha para o usuário administrador de sua aplicação.
Use o mesmo e-mail de sua conta de acesso ao YouTube para que esse usuário
também seja o administrador da aplicação.

Concluído o passo anterior você deve ter acesso à sua aplicação pelo endereço
`http://localhost/site`. Acesse `http://localhost/site/login-panel` para efetuar
o primeiro login usando o e-mail e a senha do administrador. Em seguida, clique
no ícone de engrenagem (no canto superior direito) para acessar o menu
`Configurações` e inserir os dados de sua aplicação e de seu canal no YouTube.

**Observação**

Antes de começar a modificar sua aplicação eu recomendo alterar as permissões
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

### Instalação no Windows

[Baixe os arquivos](https://github.com/lcmaquino/minhaacademia/archive/main.zip)
do repositório e coloque no diretório `C:\xampp`. Você deve ficar com uma
estrutura de diretórios contendo algo como:
```
C:\xampp
| - minhaacademia
| - htdocs
|   | - site
| ...
```

Antes de continuar a instalação você precisa ter permissões de leitura e
escrita nos diretórios `C:\xampp\minhaacademia` e `C:\xampp\htdocs\site`.

No Windows você pode rodar o painel de controle do XAMPP como administrador.
Desse modo, ao iniciar o servidor Apache você terá permissão de leitura e
escrita.

Em seguida, abra um prompt de comando para rodar o Composer e baixar todos
os arquivos necessários:
```
cd C:\xampp\minhaacademia
composer install
```

Aguarde o Composer baixar e instalar todas as dependências.

Dentro do diretório `C:\xampp\minhaacademia` crie o arquivo `.env` para
configurar o ambiente de sua aplicação. O arquivo
`C:\xampp\minhaacademia\.env.example` serve como modelo.

Abra o phpMyAdmin digitando no seu navegador o endereço 
`http://localhost/phpmyadmin`. Crie um banco de dados `minhaacademia`
com codificação de caracteres (*collation*) `utf8_general_ci`. 
Conceda todas as permissões desse banco para o usuário `USUARIO`
com senha `SENHA` (trocando `USUARIO` e `SENHA` conforme sua preferência).
Em seguida, modifique os seguintes parâmetros no arquivo
`C:\xampp\minhaacademia\.env` para configurar a autenticação no banco de
dados.
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=minhaacademia
DB_USERNAME=USUARIO
DB_PASSWORD=SENHA
```

Abra o seu navegador e digite o endereço `http://localhost/site/install.php`.
Escolha um e-mail e uma senha para o usuário administrador de sua aplicação.
Use o mesmo e-mail de sua conta de acesso ao YouTube para que esse usuário
também seja o administrador da aplicação.

Concluído o passo anterior você deve ter acesso à sua aplicação pelo endereço
`http://localhost/site`. Acesse `http://localhost/site/login-panel` para efetuar
o primeiro login usando o e-mail e a senha do administrador. Em seguida, clique
no ícone de engrenagem (no canto superior direito) para acessar o menu
`Configurações` e inserir os dados de sua aplicação e de seu canal no YouTube.

### Aplicação web no Google Console

Para que o usuário possa efetuar o login na sua aplicação usando a conta dele no
YouTube, você precisa criar um projeto de aplicação web no Google Console. Crie
esse projeto em https://console.developers.google.com/ e ative a 
"YouTube Data API v3". Na "Tela de consentimento OAuth" use os escopos
`userinfo.email`, `openid` e `youtube.readonly`. Adicione uma credencial
OAuth 2.0 e configure o endereço `https://localhost/site/login/google/callback`
como um URI de redirecionamento autorizado.

**Observação**

Devido ao uso do escopo `youtube.readonly` o seu projeto de
aplicação web terá que passar por um processo de verificação por um time do
Google. Enquanto a sua aplicação não for aprovada na verificação, o usuário verá
uma tela chamada "App não verificado" durante o processo de login. Só é
necessário submeter sua aplicação para a verificação depois que você concluir
todas as alterações que desejar fazer na mesma.

### Ativar anti spam no formulário de contato

Se você quiser habilitar o reCAPTCHA v3 no seu formulário de contato
para evitar spam, acesse o endereço https://www.google.com/recaptcha/ e no
console administrativo crie as chaves. Depois insira essas chaves na página de
`Configurações` da sua aplicação.

## Instalação em servidor de hospedagem

Você deve basicamente replicar no servidor todos os procedimentos explicados
na seção **Instalação local**. Provavelmente o diretório público do seu servidor
será diferente de `htdocs`. Nesse caso, supondo que ele seja `public_html`, a
sua estrutura de diretórios deve ficar contendo algo como:
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

Após realizar todos os outros procedimentos conforme **Instalação local**,
coloque sua aplicação em ambiente de produção e desative as mensagens de
*debug* usando os parâmetros `APP_ENV` e `APP_DEBUG` no arquivo
`/home/USUARIO/minhaacademia/.env`.
```
APP_ENV=production
APP_DEBUG=false
```

**Observação**

Em alguns servidores de hospedagem existem limitações de acesso ao terminal ou
configurações avançadas. Nesse caso você pode fazer uma **Instalação local**
no seu computador e depois copiar os arquivos para o servidor de hospedagem.

Lembre que você terá que configurar o diretório público do servidor conforme
explicado acima. Além disso, você precisará criar um link simbólico para o
diretório `/home/USUARIO/minhaacademia/storage/app/public`. Para fazer isso,
você pode criar no diretório público de seu servidor um arquivo
`link-simbolico.php` com o código abaixo.
```
<?php
  $target = '/home/USUARIO/minhaacademia/storage/app/public';
  $link = '/home/USUARIO/public_html/site/storage';
  symlink($target, $link);
```

Execute esse código acessando `seudominio.com.br/link-simbolico.php`. Se ele
funcionar, então você verá uma página em branco e o diretório
`/home/USUARIO/public_html/site/storage` foi criado no seu servidor. Caso
contrário, serão exibidas mensagens de erro na página. Depois que o link
estiver criado você pode remover o arquivo `link-simbolico.php` do
diretório público.

## Licença

A Minha Academia é um programa de código aberto licenciado sob a 
[GPL v3.0 ou posterior](https://github.com/lcmaquino/minhaacademia/blob/main/LICENSE).
