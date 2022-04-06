$(document).ready(() => {

    let data = new Date()
    let mes = data.getMonth()
    let ano = data.getFullYear()
    let dataAtual = ano+'-'+mes

    $.ajax({
        type: 'GET',
        url: 'app.php',
        data: `competencia=${dataAtual}`,
        dataType: 'json',
        success: dados => {
            $('#clientesAtivos').html(dados.clientesAtivos);
            $('#clientesInativos').html(dados.clientesInativos);
            $('#totalReclamacoes').html(dados.totalReclamacoes);
            $('#totalElogios').html(dados.totalElogios);
            $('#totalSugestoes').html(dados.totalSugestoes);
        },
        error: erro => {console.log(erro)}
    })

    $('#documentacao').on('click', () => {
        //$('#pagina').load('documentacao.html')
        /*
        $.get('documentacao.html', data => {
            $('#pagina').html(data)
        })
        */
        $.post('documentacao.html', data => {
            $('#pagina').html(data)
        })
    })

    $('#suporte').on('click', () => {
        //$('#pagina').load('suporte.html')
        /*
        $.get('suporte.html', data => {
            $('#pagina').html(data)
        })
        */
        $.post('suporte.html', data => {
            $('#pagina').html(data)
        })
    })

    $('#dashboard').on('click', () => {
        location.reload()
    })


    $('#competencia').on('change', (e) => {
        //recupera a data
        let competencia = $(e.target).val()

        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${competencia}`,
            dataType: 'json',
            success: dados => {
                $('#numeroVendas').html(dados.numeroVendas);
                $('#totalVendas').html(dados.totalVendas);
                $('#totalDespesas').html(dados.totalDespesas);
            },
            error: erro => {console.log(erro)}
        })
        //metodo, url, dados, sucesso
    })



})