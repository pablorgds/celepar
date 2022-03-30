//Função para carregar os municípios de acordo com a UF selecionada
$.fn.carregaMunicipios = function(id) {
	$.ajax({			
		type: 'POST',
		url: '../neocep/listarMunicipio',
		data: 'uf='+ $('#uf').val(),
		dataType: "json",
		success: function(json) {
			var options = '<option value="">--Escolha--</option>';
            $.each(json, function(key, value){
               options += '<option value="' + value[0] + '">' + value[4] + '</option>';
            });
            $("#municipio").html(options);
            
            if(id){
            	$("#municipio").val(id);
            }            
		}
	})
}

//Função para carregar as UFs
$.fn.carregaUF = function(id) {
	$.ajax({			
		type: 'POST',
		url: '../neocep/listarUF',
		dataType: "json",
		success: function(json) {
			var options = '<option value="">--Escolha--</option>';
            $.each(json, function(key, value){
               options += '<option value="' + value[0] + '">' + value[2] + '</option>';
            });
            $("#uf").html(options);
		}
	})
}

//Busca as informações do endereço pelo CEP informado
$.fn.buscaPorCep = function() {
	
	var cep = $('#cep').val();
	if(cep != ''){
		
		/*ENQUANTO NÃO É DISPONIBILIZADO O SERVIÇO VIA JSON, É NECESSÁRIO IMPLEMENTAR VIA SOAP*/
		$.ajax({
			type: 'POST',
			url: '../neocep/buscaPorCep',
			data: 'cep='+ $('#cep').val(),
			success: function(data) {
				$('#endereco').val(data.logradouro);
				$("#bairro").val(data.bairro);
				$("#uf").val(data.uf);
				$().carregaMunicipios(data.municipio);
			},
			error: function(){
                alert('Erro no Ajax !');
            }				
		})
		/*FIM MÉTODO SOAP*/
		
	
		/* AQUI TRABALHA COM JSON DIRETAMENTE, FUTURAMENTO O COMPONENTE IRÁ TRABALHAR DESTA FORMA
		var url = 'http://cep.correiocontrol.com.br/'+cep+'.json';
		$.getJSON(url, function(json){
			$("#endereco").val(json.logradouro);
			$("#bairro").val(json.bairro);
			...continuar a implementar
		})
		*/
		
	}
	else{
		alert('Insira um CEP Válido.');
	}
}

//Bloqueia a tela enquanto o Ajax processa a requisição
$.fn.bloquearTela = function() {
	$(document)
	.ajaxStart( $.blockUI({ message: '<h1><img src="../packages/celepar/light/assets/img/busy.gif" /> Aguarde...</h1>' }) )
	.ajaxStop($.unblockUI);
}


//Executa as funções
$(document).ready(function()
{
	
	//Verifica se existe o campo UF e chama o WS para carrega-lo
	if( $('#uf').length ){		
		$().bloquearTela();
		$().carregaUF();		
	}
	
	//Ao selecionar um UF, lista todas os municipios da mesma
	$('#uf').change(function() {
		$().bloquearTela();
		$().carregaMunicipios(); 	
	});
	
	//Busca os dados do endereço pelo CEP informado
	$('#neoCepBuscarPorCep').click(function() {
		$().bloquearTela();
		$().buscaPorCep();
	});	
});