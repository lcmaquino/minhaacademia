@extends('layouts.panelBase')
@section('content-panel')
    @if ($errors->any())
    <div class="alert alert-danger">
        <p>Ops! Aconteceu algum erro. Role a página para ver as mensagens de erro.</p>
    </div>
    @endif

    <form action="{{ route('settingUpdate') }}" method="post">
        @csrf
        @method('PUT')
        <div class="u-full-width text-left">
            <h5>Aplicação</h5>
            <label for="appName" class="u-pull-left">Nome:</label>
            <input type="text" class="u-full-width @error('appName') is-invalid @enderror" name="appName" id="appName" value="{{ old('appName') ? old('appName') : $appName }}">
            @error('appName')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="appUrl" class="u-pull-left">URL:</label>
            <input type="url" class="u-full-width @error('appUrl') is-invalid @enderror" name="appUrl" id="appUrl" value="{{ old('appUrl') ? old('appUrl') : $appUrl }}">
            @error('appUrl')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="appContactMail" class="u-pull-left">E-mail de contato:</label>
            <input type="mail" class="u-full-width @error('appContactMail') is-invalid @enderror" name="appContactMail" id="appContactMail" value="{{ old('appContactMail') ? old('appContactMail') : $appContactMail }}">
            @error('appContactMail')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="defaultLogin" class="u-pull-left">Login Padrão:</label>
            <select name="defaultLogin" class="u-full-width" id="defaultLogin">
                <option value="form" {{ (old('defaulLogin') === 'form' || $defaultLogin === 'form') ? 'selected' : '' }}>Formulário</option>
                <option value="oauth2" {{ (old('defaultLogin') === 'oauth2' || $defaultLogin === 'oauth2') ? 'selected' : '' }}>OAuth 2.0</option>
            </select>
            <br>

            <label for="donationUrl" class="u-pull-left">Doação (URL):</label>
            <input type="text" class="u-full-width @error('donationUrl') is-invalid @enderror" name="donationUrl" id="donationUrl" value="{{ old('donationUrl') ? old('donationUrl') : $donationUrl }}">
            <p class="information"><small(>(*) Campo Opcional. URL para uma campanha de doação.</small></p>
            @error('donationUrl')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            @if ($defaultLogin === 'oauth2')
                <hr>

                <h5>Cliente OAtuh 2.0</h5>
                <p class="information"><small>Configure o cliente de sua aplicação em <a href="https://console.developers.google.com/">Google Console</a>.</small></p>

                <label for="googleClientId" class="u-pull-left">Google Client ID:</label>
                <input type="text" class="u-full-width @error('googleClientId') is-invalid @enderror" name="googleClientId" id="googleClientId" value="{{ old('googleClientId') ? old('googleClientId') : $googleClientId }}">
                @error('googleClientId')
                <span class="alert is-invalid">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
                <br>

                <label for="googleClientSecret" class="u-pull-left">Google Client Secret:</label>
                <input type="text" class="u-full-width @error('googleClientSecret') is-invalid @enderror" name="googleClientSecret" id="googleClientSecret" value="{{ old('googleClientSecret') ? old('googleClientSecret') : $googleClientSecret }}">
                @error('googleClientSecret')
                <span class="alert is-invalid">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
                <br>

                <label for="googleRedirectUri" class="u-pull-left">Google Redirect URI:</label>
                <input type="text" class="u-full-width @error('googleRedirectUri') is-invalid @enderror" name="googleRedirectUri" id="googleRedirectUri" value="{{ old('googleRedirectUri') ? old('googleRedirectUri') : $googleRedirectUri }}">
                @error('googleRedirectUri')
                <span class="alert is-invalid">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
                <br>
            @endif

            <hr>

            <h5>Certificados</h5>

            <label for="minScore" class="u-pull-left">Pontuação Mínima nas Atividades:</label>
            <input type="number" class="u-full-width @error('minScore') is-invalid @enderror" name="minScore" id="minScore" value="{{ old('minScore') ? old('minScore') : $minScore }}">
            @error('minScore')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="certifySignatureName" class="u-pull-left">Nome na assinatura:</label>
            <input type="text" class="u-full-width @error('certifySignatureName') is-invalid @enderror" name="certifySignatureName" id="certifySignatureName" value="{{ old('certifySignatureName') ? old('certifySignatureName') : $certifySignatureName }}">
            @error('certifySignatureName')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="certifyState" class="u-pull-left">Local (Estado):</label>
            <input type="text" class="u-full-width @error('certifyState') is-invalid @enderror" name="certifyState" id="certifyState" value="{{ old('certifyState') ? old('certifyState') : $certifyState }}">
            @error('certifyState')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <hr>

            <h5>Serviço de E-mail</h5>

            <p class="information"><small>Os e-mails são enviados via SMTP.</small></p>

            <label for="mailHost" class="u-pull-left">Host:</label>
            <input type="text" class="u-full-width @error('mailHost') is-invalid @enderror" name="mailHost" id="mailHost" value="{{ old('mailHost') ? old('mailHost') : $mailHost }}">
            @error('mailHost')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="mailPort" class="u-pull-left">Porta:</label>
            <input type="number" class="u-full-width @error('mailPort') is-invalid @enderror" name="mailPort" id="mailPort" value="{{ old('mailPort') ? old('mailPort') : $mailPort }}">
            @error('mailPort')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="mailUsername" class="u-pull-left">Conta do usuário:</label>
            <input type="mail" class="u-full-width @error('mailUsername') is-invalid @enderror" name="mailUsername" id="mailUsername" value="{{ old('mailUsername') ? old('mailUsername') : $mailUsername }}">
            @error('mailUsername')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            @if ($isPasswordEmpty)
                <label for="mailPassword" class="u-pull-left">Senha:</label>
                <input type="password" class="u-full-width @error('mailPassword') is-invalid @enderror" name="mailPassword" id="mailPassword" value="">
                @error('mailPassword')
                <span class="alert is-invalid">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            @else
                <p><strong>Senha:</strong> <a href="{{ route('settingClearUserMailPassowrd') }}">Limpar senha</a></p>
            @endif
            <br>

            <label for="mailEncryption" class="u-pull-left">Criptografia:</label>
            <select name="mailEncryption" class="u-full-width" id="mailEncryption">
                <option value="tls" {{ (old('mailEncryption') === 'tls' || $mailEncryption === 'tls') ? 'selected' : '' }}>TLS</option>
                <option value="ssl" {{ (old('mailEncryption') === 'ssl' || $mailEncryption === 'ssl') ? 'selected' : '' }}>SSL</option>
            </select>

            <label for="mailFromAddress" class="u-pull-left">E-mail do Remetente:</label>
            <input type="mail" class="u-full-width @error('mailFromAddress') is-invalid @enderror" name="mailFromAddress" id="mailFromAddress" value="{{ old('mailFromAddress') ? old('mailFromAddress') : $mailFromAddress }}">
            @error('mailFromAddress')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="mailFromName" class="u-pull-left">Nome do Remetente:</label>
            <input type="text" class="u-full-width @error('mailFromName') is-invalid @enderror" name="mailFromName" id="mailFromName" value="{{ old('mailFromName') ? old('mailFromName') : $mailFromName }}">
            @error('mailFromName')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="mailToAddress" class="u-pull-left">E-mail do destinatário:</label>
            <input type="mail" class="u-full-width @error('mailToAddress') is-invalid @enderror" name="mailToAddress" id="mailToAddress" value="{{ old('mailToAddress') ? old('mailToAddress') : $mailToAddress }}">
            @error('mailToAddress')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <hr>

            <h5>Canal do YouTube</h5>

            <label for="youtubeChannelId" class="u-pull-left">ID:</label>
            <input type="text" class="u-full-width @error('youtubeChannelId') is-invalid @enderror" name="youtubeChannelId" id="youtubeChannelId" value="{{ old('youtubeChannelId') ? old('youtubeChannelId') : $youtubeChannelId }}">
            <p class="information"><small>Exemplo: youtube.com/channel/<strong>abcd</strong>, ID: <strong>abcd</strong>.</small></p>
            @error('youtubeChannelId')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="youtubeChannelTitle" class="u-pull-left">Título:</label>
            <input type="text" class="u-full-width @error('youtubeChannelTitle') is-invalid @enderror" name="youtubeChannelTitle" id="youtubeChannelTitle" value="{{ old('youtubeChannelTitle') ? old('youtubeChannelTitle') : $youtubeChannelTitle }}">
            @error('youtubeChannelTitle')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="youtubeChannelUrl" class="u-pull-left">URL:</label>
            <input type="text" class="u-full-width @error('youtubeChannelUrl') is-invalid @enderror" name="youtubeChannelUrl" id="youtubeChannelUrl" value="{{ old('youtubeChannelUrl') ? old('youtubeChannelUrl') : $youtubeChannelUrl }}">
            @error('youtubeChannelUrl')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="youtubeChannelDefaultVideo" class="u-pull-left">Vídeo padrão:</label>
            <input type="text" class="u-full-width @error('youtubeChannelDefaultVideo') is-invalid @enderror" name="youtubeChannelDefaultVideo" id="youtubeChannelDefaultVideo" value="{{ old('youtubeChannelDefaultVideo') ? old('youtubeChannelDefaultVideo') : $youtubeChannelDefaultVideo }}">
            <p class="information"><small>Exemplo: youtube.com/watch?v=<strong>abcd</strong>, Vídeo padrão: <strong>abcd</strong>.</small></p>
            @error('youtubeChannelDefaultVideo')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="youtubeChannelAbout" class="u-pull-left">Sobre:</label>
            <textarea id="youtubeChannelAbout" name="youtubeChannelAbout" class="u-full-width @error('youtubeChannelAbout') is-invalid @enderror" placeholder="Escreva sobre seu canal…">{{ old('youtubeChannelAbout') ?  old('youtubeChannelAbout') : $youtubeChannelAbout }}</textarea>
            <p class="information"><small><strong>Atenção</strong>: use no máximo 488 caracteres.</small></p>
            @error('youtubeChannelAbout')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <hr>

            <h5>Mídia social</h5>

            <p class="information"><small>Insira apenas o usuário de cada rede. Exemplo: instagram.com/meu_usuario, campo Instagram: meu_usuario.</small></p>
            
            <label for="socialMediaFacebook" class="u-pull-left">Facebook:</label>
            <input type="text" class="u-full-width @error('socialMediaFacebook') is-invalid @enderror" name="socialMediaFacebook" id="socialMediaFacebook" value="{{ old('socialMediaFacebook') ? old('socialMediaFacebook') : $socialMediaFacebook }}">
            @error('socialMediaFacebook')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="socialMediaInstagram" class="u-pull-left">Instagram:</label>
            <input type="text" class="u-full-width @error('socialMediaInstagram') is-invalid @enderror" name="socialMediaInstagram" id="socialMediaInstagram" value="{{ old('socialMediaInstagram') ? old('socialMediaInstagram') : $socialMediaInstagram }}">
            @error('socialMediaInstagram')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="socialMediaTwitter" class="u-pull-left">Twitter:</label>
            <input type="text" class="u-full-width @error('socialMediaTwitter') is-invalid @enderror" name="socialMediaTwitter" id="socialMediaTwitter" value="{{ old('socialMediaTwitter') ? old('socialMediaTwitter') : $socialMediaTwitter }}">
            @error('socialMediaTwitter')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <hr>

            <h5>Google reCAPTCHA v3</h5>

            <p class="information"><small>Evite <em>spam</em> no formulário de contato usando <a href="https://www.google.com/recaptcha">Google reCAPTCHA v3</a>.</small></p>

            <label for="googleRecaptchaSiteKey" class="u-pull-left">Chave do Site:</label>
            <input type="text" class="u-full-width @error('googleRecaptchaSiteKey') is-invalid @enderror" name="googleRecaptchaSiteKey" id="googleRecaptchaSiteKey" value="{{ old('googleRecaptchaSiteKey') ? old('googleRecaptchaSiteKey') : $googleRecaptchaSiteKey }}">
            @error('googleRecaptchaSiteKey')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <label for="googleRecaptchaSecretKey" class="u-pull-left">Segredo do Site:</label>
            <input type="text" class="u-full-width @error('googleRecaptchaSecretKey') is-invalid @enderror" name="googleRecaptchaSecretKey" id="googleRecaptchaSecretKey" value="{{ old('googleRecaptchaSecretKey') ? old('googleRecaptchaSecretKey') : $googleRecaptchaSecretKey }}">
            @error('googleRecaptchaSecretKey')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>

            <input type="submit" value="Salvar">
        </div>
    </form>

    <hr>

    <div class="u-full-width text-left">
        <h5>Versão da aplicação</h5>
        <p>{{ $applicationVersion }}</p>
        @if($hasUpdate)
            <p id="update-button"><a href="#" class="button" onclick="load_home(event, '{{ route('updateApplication') }}'); return false;">Atualizar</a></p>
            <script>
                function load_home(e, uri){
                    e.preventDefault();
                    var updateButton = document.getElementById('update-button');
                    var aux = updateButton.innerHTML;
                    updateButton.innerHTML += ' <img src="{{ asset('img/loading.gif') }}" alt="Carregando…" height="32" style="vertical-align:middle;">'
                    var con = document.getElementById('content');
                    var xhr = new XMLHttpRequest();
                    con.innerHTML = '';
                    xhr.open("GET", uri, true);
                    xhr.setRequestHeader('Content-type', 'text/html');
                    xhr.onreadystatechange = function(e) {
                        if(xhr.readyState == 3 || xhr.readyState == 4) {
                            con.innerHTML = xhr.responseText;
                        }
                        if(xhr.readyState == 4) {
                            updateButton.innerHTML = aux;
                        }
                    }
                    xhr.send();
                }
                </script>                
            <div id="content"></div>
        @elseif($hasUpdate === null)
            <p>Não foi possível verificar se há atualizações. Tente novamente mais tarde.</p>
        @else
            <p>Nenhuma atualização disponível.</p>
        @endif

        <hr>
        
        <h5>Licenças de código aberto</h5>
        <p><a href="https://github.com/laravel/framework/blob/v{{ $laravelVersion }}/LICENSE.md">Laravel {{ $laravelVersion }}</a></p>
    </div>
@endsection