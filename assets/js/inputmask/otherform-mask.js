(function($) {
    $(window).load( function() {
		// Data
		$(".mascara_data").addClass('date');
		$("#mascara_data").addClass('date');
		$("#form-field-mascara_data").addClass('date');
		$("#form-field-mascara_data2").addClass('date');
		$("#form-field-mascara_data3").addClass('date');
		$("#form-field-mascara_data4").addClass('date');	
		
		// Hora
		$(".mascara_hora").addClass('time');
		$("#mascara_hora").addClass('time');
		$("#form-field-mascara_hora").addClass('time');
		$("#form-field-mascara_hora2").addClass('time');
		$("#form-field-mascara_hora3").addClass('time');
		$("#form-field-mascara_hora4").addClass('time');
		
		// Data e Hora
		$(".mascara_data_hora").addClass('date_time');
		$("#mascara_data_hora").addClass('date_time');
		$("#form-field-mascara_data_hora").addClass('date_time');
		$("#form-field-mascara_data_hora2").addClass('date_time');
		$("#form-field-mascara_data_hora3").addClass('date_time');
		$("#form-field-mascara_data_hora4").addClass('date_time');
		
		// CEP
		$(".mascara_cep").addClass('cep');
		$("#mascara_cep").addClass('cep');
		$("#form-field-mascara_cep").addClass('cep');
		$("#form-field-mascara_cep2").addClass('cep');
		$("#form-field-mascara_cep3").addClass('cep');
		$("#form-field-mascara_cep4").addClass('cep');
		
		// Telefone
		$(".mascara_telefone").addClass('phone');
		$("#mascara_telefone").addClass('phone');
		$("#form-field-mascara_telefone").addClass('phone');
		$("#form-field-mascara_telefone2").addClass('phone');
		$("#form-field-mascara_telefone3").addClass('phone');
		$("#form-field-mascara_telefone4").addClass('phone');
		
		// Telefone com DDD
		$(".mascara_telefone_ddd").addClass('telephone_with_ddd');
		$("#mascara_telefone_ddd").addClass('telephone_with_ddd');
		$("#form-field-mascara_telefone_ddd").addClass('telephone_with_ddd');
		$("#form-field-mascara_telefone_ddd2").addClass('telephone_with_ddd');
		$("#form-field-mascara_telefone_ddd3").addClass('telephone_with_ddd');
		$("#form-field-mascara_telefone_ddd4").addClass('telephone_with_ddd');

		// Telefone com nono digito
		$(".mascara_telefone_nono_digito").addClass('phone_with_ddd');
		$("#mascara_telefone_nono_digito").addClass('phone_with_ddd');
		$("#form-field-mascara_telefone_nono_digito").addClass('phone_with_ddd');
		$("#form-field-mascara_telefone_nono_digito2").addClass('phone_with_ddd');
		$("#form-field-mascara_telefone_nono_digito3").addClass('phone_with_ddd');
		$("#form-field-mascara_telefone_nono_digito4").addClass('phone_with_ddd');
				
		// CPF ou CNPJ
		$(".mascara_cpf_ou_cnpj").addClass('cpfcnpj');
		$("#mascara_cpf_ou_cnpj").addClass('cpfcnpj');
		$("#form-field-mascara_cpf_ou_cnpj").addClass('cpfcnpj');
		$("#form-field-mascara_cpf_ou_cnpj2").addClass('cpfcnpj');
		$("#form-field-mascara_cpf_ou_cnpj3").addClass('cpfcnpj');
		$("#form-field-mascara_cpf_ou_cnpj4").addClass('cpfcnpj');
		
		// CPF
		$(".mascara_cpf").addClass('cpf');
		$("#mascara_cpf").addClass('cpf');
		$("#form-field-mascara_cpf").addClass('cpf');
		$("#form-field-mascara_cpf2").addClass('cpf');
		$("#form-field-mascara_cpf3").addClass('cpf');
		$("#form-field-mascara_cpf4").addClass('cpf');
		
		// CNPJ
		$(".mascara_cnpj").addClass('cnpj');
		$("#mascara_cnpj").addClass('cnpj');
		$("#form-field-mascara_cnpj").addClass('cnpj');
		$("#form-field-mascara_cnpj2").addClass('cnpj');
		$("#form-field-mascara_cnpj3").addClass('cnpj');
		$("#form-field-mascara_cnpj4").addClass('cnpj');			
		
		// Monetário
		$(".mascara_monetario").addClass('money');
		$("#mascara_monetario").addClass('money');
		$("#form-field-mascara_monetario").addClass('money');
		$("#form-field-mascara_monetario2").addClass('money');
		$("#form-field-mascara_monetario3").addClass('money');
		$("#form-field-mascara_monetario4").addClass('money');		

		// IP
		$(".mascara_ip").addClass('ip_address');
		$("#mascara_ip").addClass('ip_address');
		$("#form-field-mascara_ip").addClass('ip_address');
		$("#form-field-mascara_ip2").addClass('ip_address');
		$("#form-field-mascara_ip3").addClass('ip_address');
		$("#form-field-mascara_ip4").addClass('ip_address');
			
		// Porcentagem
		$(".mascara_porcentagem").addClass('percent');
		$("#mascara_porcentagem").addClass('percent');
		$("#form-field-mascara_porcentagem").addClass('percent');
		$("#form-field-mascara_porcentagem2").addClass('percent');
		$("#form-field-mascara_porcentagem3").addClass('percent');
		$("#form-field-mascara_porcentagem4").addClass('percent');

		// Placa de veículos
		$(".mascara_placa").addClass('placa');
		$("#mascara_placa").addClass('placa');
		$("#form-field-mascara_placa").addClass('placa');
		$("#form-field-mascara_placa2").addClass('placa');
		$("#form-field-mascara_placa3").addClass('placa');
		$("#form-field-mascara_placa4").addClass('placa');
		
		// Nome de Usuário
		$(".mascara_usuario").addClass('user_chars');
		$("#mascara_usuario").addClass('user_chars');
		$("#form-field-mascara_usuario").addClass('user_chars');
		$("#form-field-mascara_usuario2").addClass('user_chars');
		$("#form-field-mascara_usuario3").addClass('user_chars');
		$("#form-field-mascara_usuario4").addClass('user_chars');

		// Número Cartão de Crédito
		$(".mascara_cartaon").addClass('card_number');
		$("#mascara_cartaon").addClass('card_number');
		$("#form-field-mascara_cartaon").addClass('card_number');
		$("#form-field-mascara_cartaon2").addClass('card_number');
		$("#form-field-mascara_cartaon3").addClass('card_number');
		$("#form-field-mascara_cartaon4").addClass('card_number');
		
		// Validade Cartão de Crédito
		$(".mascara_cartaod").addClass('card_date');
		$("#mascara_cartaod").addClass('card_date');
		$("#form-field-mascara_cartaod").addClass('card_date');
		$("#form-field-mascara_cartaod2").addClass('card_date');
		$("#form-field-mascara_cartaod3").addClass('card_date');
		$("#form-field-mascara_cartaod4").addClass('card_date');	

		// Máscaras
		$('.date').mask('00/00/0000');
		$('.time').mask('00:00:00');
		$('.date_time').mask('00/00/0000 00:00:00');
		$('.cep').mask('00000-000');
		$('.phone').mask('0000-0000');
		$('.telephone_with_ddd').mask('(00) 0000-0000'); 
		$('.phone_with_ddd').mask('(00) 00000-0000');
			var maskBehavior = function (val) {
				return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
			},
				options = {onKeyPress: function(val, e, field, options) {
					field.mask(maskBehavior.apply({}, arguments), options);
				}
			};
		$('.phone_with_ddd').mask(maskBehavior, options);
			var options = {
				onKeyPress : function(cpfcnpj, e, field, options) {
					var masks = ['000.000.000-009', '00.000.000/0000-00'];
					var mask = (cpfcnpj.length > 14) ? masks[1] : masks[0];
					$('.cpfcnpj').mask(mask, options);
				}
			};
		$('.cpfcnpj').mask('000.000.000-009', options);
		$('.cpf').mask('000.000.000-00', {reverse: true});
		$('.cnpj').mask('00.000.000/0000-00', {reverse: true});
		$('.money').mask('000.000.000.000.000,00', {reverse: true});
		$('.ip_address').mask('099.099.099.099');
		$('.percent').mask('##0,00%', {reverse: true});
			var MercoSulMaskBehavior = function (val) { 
				var placams = val.replace(/[^a-zA-Z0-9]/g, '');
				if(placams.length < 5) return 'SSS-0A00';
				return placams.charAt(4).replace(/\d/g, '').length == 1 ? 'SSS0S00' : 'SSS-0000';
			},
			spOptions = {
			  onKeyPress: function(val, e, field, options) {
				  field.mask(MercoSulMaskBehavior.apply({}, arguments), options);
				  e.target.value=val.toUpperCase();
				}
			};
		$('.placa').mask(MercoSulMaskBehavior, spOptions);
		$('.user_chars').mask('Z',{translation: {'Z': {pattern: /[a-z0-9_-]/, recursive: true}}});
		$('.card_number').mask('0000-0000-0000-0000');
		$('.card_date').mask('00/00');		
		$('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
		$('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
		$('.fallback').mask("00r00r0000", {
		  translation: {
			'r': {
			  pattern: /[\/]/,
			  fallback: '/'
			},
			placeholder: "__/__/____"
		  }
		});
		$('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});

		jQuery( document ).on( 'elementor/popup/show', () => {

			// Data
			$(".mascara_data").addClass('date');
			$("#mascara_data").addClass('date');
			$("#form-field-mascara_data").addClass('date');
			$("#form-field-mascara_data2").addClass('date');
			$("#form-field-mascara_data3").addClass('date');
			$("#form-field-mascara_data4").addClass('date');	
			
			// Hora
			$(".mascara_hora").addClass('time');
			$("#mascara_hora").addClass('time');
			$("#form-field-mascara_hora").addClass('time');
			$("#form-field-mascara_hora2").addClass('time');
			$("#form-field-mascara_hora3").addClass('time');
			$("#form-field-mascara_hora4").addClass('time');
			
			// Data e Hora
			$(".mascara_data_hora").addClass('date_time');
			$("#mascara_data_hora").addClass('date_time');
			$("#form-field-mascara_data_hora").addClass('date_time');
			$("#form-field-mascara_data_hora2").addClass('date_time');
			$("#form-field-mascara_data_hora3").addClass('date_time');
			$("#form-field-mascara_data_hora4").addClass('date_time');
			
			// CEP
			$(".mascara_cep").addClass('cep');
			$("#mascara_cep").addClass('cep');
			$("#form-field-mascara_cep").addClass('cep');
			$("#form-field-mascara_cep2").addClass('cep');
			$("#form-field-mascara_cep3").addClass('cep');
			$("#form-field-mascara_cep4").addClass('cep');
			
			// Telefone
			$(".mascara_telefone").addClass('phone');
			$("#mascara_telefone").addClass('phone');
			$("#form-field-mascara_telefone").addClass('phone');
			$("#form-field-mascara_telefone2").addClass('phone');
			$("#form-field-mascara_telefone3").addClass('phone');
			$("#form-field-mascara_telefone4").addClass('phone');
			
			// Telefone com DDD
			$(".mascara_telefone_ddd").addClass('telephone_with_ddd');
			$("#mascara_telefone_ddd").addClass('telephone_with_ddd');
			$("#form-field-mascara_telefone_ddd").addClass('telephone_with_ddd');
			$("#form-field-mascara_telefone_ddd2").addClass('telephone_with_ddd');
			$("#form-field-mascara_telefone_ddd3").addClass('telephone_with_ddd');
			$("#form-field-mascara_telefone_ddd4").addClass('telephone_with_ddd');

			// Telefone com nono digito
			$(".mascara_telefone_nono_digito").addClass('phone_with_ddd');
			$("#mascara_telefone_nono_digito").addClass('phone_with_ddd');
			$("#form-field-mascara_telefone_nono_digito").addClass('phone_with_ddd');
			$("#form-field-mascara_telefone_nono_digito2").addClass('phone_with_ddd');
			$("#form-field-mascara_telefone_nono_digito3").addClass('phone_with_ddd');
			$("#form-field-mascara_telefone_nono_digito4").addClass('phone_with_ddd');
					
			// CPF ou CNPJ
			$(".mascara_cpf_ou_cnpj").addClass('cpfcnpj');
			$("#mascara_cpf_ou_cnpj").addClass('cpfcnpj');
			$("#form-field-mascara_cpf_ou_cnpj").addClass('cpfcnpj');
			$("#form-field-mascara_cpf_ou_cnpj2").addClass('cpfcnpj');
			$("#form-field-mascara_cpf_ou_cnpj3").addClass('cpfcnpj');
			$("#form-field-mascara_cpf_ou_cnpj4").addClass('cpfcnpj');
			
			// CPF
			$(".mascara_cpf").addClass('cpf');
			$("#mascara_cpf").addClass('cpf');
			$("#form-field-mascara_cpf").addClass('cpf');
			$("#form-field-mascara_cpf2").addClass('cpf');
			$("#form-field-mascara_cpf3").addClass('cpf');
			$("#form-field-mascara_cpf4").addClass('cpf');
			
			// CNPJ
			$(".mascara_cnpj").addClass('cnpj');
			$("#mascara_cnpj").addClass('cnpj');
			$("#form-field-mascara_cnpj").addClass('cnpj');
			$("#form-field-mascara_cnpj2").addClass('cnpj');
			$("#form-field-mascara_cnpj3").addClass('cnpj');
			$("#form-field-mascara_cnpj4").addClass('cnpj');			
			
			// Monetário
			$(".mascara_monetario").addClass('money');
			$("#mascara_monetario").addClass('money');
			$("#form-field-mascara_monetario").addClass('money');
			$("#form-field-mascara_monetario2").addClass('money');
			$("#form-field-mascara_monetario3").addClass('money');
			$("#form-field-mascara_monetario4").addClass('money');		

			// IP
			$(".mascara_ip").addClass('ip_address');
			$("#mascara_ip").addClass('ip_address');
			$("#form-field-mascara_ip").addClass('ip_address');
			$("#form-field-mascara_ip2").addClass('ip_address');
			$("#form-field-mascara_ip3").addClass('ip_address');
			$("#form-field-mascara_ip4").addClass('ip_address');
				
			// Porcentagem
			$(".mascara_porcentagem").addClass('percent');
			$("#mascara_porcentagem").addClass('percent');
			$("#form-field-mascara_porcentagem").addClass('percent');
			$("#form-field-mascara_porcentagem2").addClass('percent');
			$("#form-field-mascara_porcentagem3").addClass('percent');
			$("#form-field-mascara_porcentagem4").addClass('percent');

			// Placa de veículos
			$(".mascara_placa").addClass('placa');
			$("#mascara_placa").addClass('placa');
			$("#form-field-mascara_placa").addClass('placa');
			$("#form-field-mascara_placa2").addClass('placa');
			$("#form-field-mascara_placa3").addClass('placa');
			$("#form-field-mascara_placa4").addClass('placa');
			
			// Nome de Usuário
			$(".mascara_usuario").addClass('user_chars');
			$("#mascara_usuario").addClass('user_chars');
			$("#form-field-mascara_usuario").addClass('user_chars');
			$("#form-field-mascara_usuario2").addClass('user_chars');
			$("#form-field-mascara_usuario3").addClass('user_chars');
			$("#form-field-mascara_usuario4").addClass('user_chars');

			// Número Cartão de Crédito
			$(".mascara_cartaon").addClass('card_number');
			$("#mascara_cartaon").addClass('card_number');
			$("#form-field-mascara_cartaon").addClass('card_number');
			$("#form-field-mascara_cartaon2").addClass('card_number');
			$("#form-field-mascara_cartaon3").addClass('card_number');
			$("#form-field-mascara_cartaon4").addClass('card_number');
			
			// Validade Cartão de Crédito
			$(".mascara_cartaod").addClass('card_date');
			$("#mascara_cartaod").addClass('card_date');
			$("#form-field-mascara_cartaod").addClass('card_date');
			$("#form-field-mascara_cartaod2").addClass('card_date');
			$("#form-field-mascara_cartaod3").addClass('card_date');
			$("#form-field-mascara_cartaod4").addClass('card_date');	

			// Máscaras
			$('.date').mask('00/00/0000');
			$('.time').mask('00:00:00');
			$('.date_time').mask('00/00/0000 00:00:00');
			$('.cep').mask('00000-000');
			$('.phone').mask('0000-0000');
			$('.telephone_with_ddd').mask('(00) 0000-0000'); 
			$('.phone_with_ddd').mask('(00) 00000-0000');
				var maskBehavior = function (val) {
					return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
				},
					options = {onKeyPress: function(val, e, field, options) {
						field.mask(maskBehavior.apply({}, arguments), options);
					}
				};
			$('.phone_with_ddd').mask(maskBehavior, options);
				var options = {
					onKeyPress : function(cpfcnpj, e, field, options) {
						var masks = ['000.000.000-009', '00.000.000/0000-00'];
						var mask = (cpfcnpj.length > 14) ? masks[1] : masks[0];
						$('.cpfcnpj').mask(mask, options);
					}
				};
			$('.cpfcnpj').mask('000.000.000-009', options);
			$('.cpf').mask('000.000.000-00', {reverse: true});
			$('.cnpj').mask('00.000.000/0000-00', {reverse: true});
			$('.money').mask('000.000.000.000.000,00', {reverse: true});
			$('.ip_address').mask('099.099.099.099');
			$('.percent').mask('##0,00%', {reverse: true});
				var MercoSulMaskBehavior = function (val) { 
					var placams = val.replace(/[^a-zA-Z0-9]/g, '');
					if(placams.length < 5) return 'SSS-0A00';
					return placams.charAt(4).replace(/\d/g, '').length == 1 ? 'SSS0S00' : 'SSS-0000';
				},
				spOptions = {
				  onKeyPress: function(val, e, field, options) {
					  field.mask(MercoSulMaskBehavior.apply({}, arguments), options);
					  e.target.value=val.toUpperCase();
					}
				};
			$('.placa').mask(MercoSulMaskBehavior, spOptions);
			$('.user_chars').mask('Z',{translation: {'Z': {pattern: /[a-z0-9_-]/, recursive: true}}});
			$('.card_number').mask('0000-0000-0000-0000');
			$('.card_date').mask('00/00');		
			$('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
			$('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
			$('.fallback').mask("00r00r0000", {
			  translation: {
				'r': {
				  pattern: /[\/]/,
				  fallback: '/'
				},
				placeholder: "__/__/____"
			  }
			});
			$('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});

		});
    });
})(jQuery);