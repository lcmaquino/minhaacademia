@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width text-left">
        <h3 class="text-center">Formatação do texto.</h3>
        <p>
            Durante a criação de cursos, aulas, atividades ou questões, 
            você pode precisar formatar partes de seu texto. Use os 
            códigos abaixo para isso.
        </p>

        <h4>Negrito</h4>
        <p>Código: <code>[b]exemplo de texto[/b]</code></p>
        <p>Resultado: {!! $filter->_addRichFormat('[b]exemplo de texto[/b]') !!}</p>

        <h4>Itálico</h4>
        <p>Código: <code>[i]exemplo de texto[/i]</code></p>
        <p>Resultado: {!! $filter->_addRichFormat('[i]exemplo de texto[/i]') !!}</p>

        <h4>Sublinhado</h4>
        <p>Código: <code>[u]exemplo de texto[/u]</code></p>
        <p>Resultado: {!! $filter->_addRichFormat('[u]exemplo de texto[/u]') !!}</p>

        <h4>Código fonte</h4>
        <p>Código: <code>[code]exemplo de texto[/code]</code></p>
        <p>Resultado: {!! $filter->_addRichFormat('[code]exemplo de texto[/code]') !!}</p>

        <h4>Lista não ordenada</h4>
        <p>Código:</p>
        <p><code>[ul]<br>
              [li]exemplo de texto[/li]<br>
              [li]exemplo de texto[/li]<br>
            [/ul]
            </code></p>
        <p>Resultado:</p>
        {!! $filter->_addRichFormat('[ul][li]exemplo de texto[/li] [li]exemplo de texto[/li][/ul]') !!}

        <h4>Lista ordenada</h4>
        <p>Código:</p>
        <p><code>[ol]<br>
              [li]exemplo de texto[/li]<br>
              [li]exemplo de texto[/li]<br>
            [/ol]
            </code></p>
        <p>Resultado:</p>
        {!! $filter->_addRichFormat('[ol][li]exemplo de texto[/li] [li]exemplo de texto[/li][/ol]') !!}

        <h4>Tabela</h4>
        <p>Código:</p>
        <p><code>[table]<br>
              [tr][th]Coluna 1[/th][th]Coluna 2[/th][/tr]<br>
              [tr][td]Texto 1[/td][td]Texto 2[/td][/tr]<br>
            [/table]
            </code></p>
        <p>Resultado:</p>
        {!! $filter->_addRichFormat('[table][tr][th]Coluna 1[/th] [th]Coluna 2[/th][/tr] [tr][td]Texto 1[/td][td]Texto 2[/td][/tr][/table]') !!}

        <h4>Imagem</h4>
        <p>Código:</p>
        <p><code>[img]img/logo.png[/img]</code></p>
        <p>Resultado:</p>
        {!! $filter->_addImages('[img]img/logo.png[/img]') !!}
        <p>
            Use o comando <code>[img width=&lt;LARGURA&gt;px height=&lt;ALTURA&gt;px align=&lt;left | center | right&gt;]</code>
            para configurar a imagem com largura medindo &lt;LARGURA&gt;, altura medindo &lt;ALTURA&gt; e
            alinhamento à esquerda (left), ao centro (center) ou à direita (right).
        </p>
        <p>Código:</p>
        <p><code>[img height=18px align=center]img/logo.png[/img]</code></p>
        <p>Resultado:</p>
        {!! $filter->_addImages('[img height=18px align=center]img/logo.png[/img]') !!}

        <h4>LaTeX</h4>
        <p>Na mesma linha do texto:</p>
        <p><code>\(\int_a^b f(x)\,dx = F(b) - F(a),\,F'(x) = f(x)\)</code></p>
        <p>Resultado:</p>
        {!! $filter->_addLaTeX("\(\int_a^b f(x)\,dx =  F(b) - F(a),\,F'(x) = f(x)\)") !!}
        <p>Como um parágrafo centralizado:</p>
        <p><code>$$\int_a^b f(x)\,dx = F(b) - F(a),\,F'(x) = f(x)$$</code></p>
        <p>Resultado:</p>
        {!! $filter->_addLaTeX("$$\int_a^b f(x)\,dx =  F(b) - F(a),\,F'(x) = f(x)$$") !!}
    </div>
</div>
@endsection