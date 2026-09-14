<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * confidence.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['activityclosed'] = 'Esta atividade não está aberta para respostas neste momento.';
$string['allowchange'] = 'Permitir que o aluno altere a resposta';
$string['allowchange_help'] = 'Quando ativado, cada nova resposta fica no histórico. O relatório compara a primeira resposta do participante com a mais recente.';
$string['anonymous'] = 'Respostas anônimas';
$string['anonymous_help'] = 'Quando ativado, o relatório não identifica os participantes. A atividade usa uma chave pseudônima para ainda conseguir comparar a primeira e a última resposta de cada participante.';
$string['anonymousparticipant'] = 'Participante anônimo ';
$string['anonymousreport'] = 'Respostas anônimas';
$string['autoreload'] = 'O relatório é atualizado automaticamente a cada 3 segundos.';
$string['backtoactivity'] = 'Voltar para a atividade';
$string['cannotlock'] = 'Não foi possível bloquear sua resposta para gravação. Tente novamente.';
$string['changeresponse'] = 'Atualize seu nível de confiança';
$string['chartarea'] = 'Área';
$string['chartbar'] = 'Barras';
$string['chartpie'] = 'Pizza';
$string['charttype'] = 'Tipo de gráfico';
$string['closedat'] = 'As respostas foram encerradas em .';
$string['confidence:addinstance'] = 'Adicionar uma atividade Nível de confiança';
$string['confidence:managereference'] = 'Registrar a referência de presença da sala';
$string['confidence:respond'] = 'Enviar respostas de nível de confiança';
$string['confidence:view'] = 'Visualizar atividades Nível de confiança';
$string['confidence:viewreport'] = 'Visualizar relatórios de nível de confiança';
$string['confidencename'] = 'Nome da atividade';
$string['declined'] = 'Diminuíram';
$string['distribution'] = 'Distribuição antes e depois';
$string['erroranonymouslocked'] = 'O modo anônimo não pode ser alterado depois que já existem respostas.';
$string['errorcloseforresults'] = 'Defina um horário de encerramento quando os resultados devem aparecer apenas após o fim da atividade.';
$string['errorlocationradius'] = 'O raio de localização deve ficar entre 1 e 5000 metros.';
$string['errortimeclose'] = 'O horário de encerramento precisa ser posterior ao horário de abertura.';
$string['firstresponse'] = 'Primeira resposta';
$string['gettinglocation'] = 'Obtendo sua localização…';
$string['groupresults'] = 'Resultado atual do grupo';
$string['improved'] = 'Aumentaram';
$string['invalidlevel'] = 'Nível de confiança inválido.';
$string['invalidlocation'] = 'A localização informada é inválida.';
$string['ipmismatch'] = 'Seu IP atual não corresponde à referência de sala registrada pelo professor.';
$string['lastupdate'] = 'Última atualização';
$string['latestresponse'] = 'Resposta mais recente';
$string['legendafter'] = 'Depois';
$string['legendbefore'] = 'Antes';
$string['level1'] = 'Não sei';
$string['level2'] = 'Tenho pouca segurança';
$string['level3'] = 'Acho que sei';
$string['level4'] = 'Domino';
$string['livereport'] = 'Relatório em tempo real';
$string['locationdenied'] = 'A permissão de localização foi negada ou não foi possível obter a localização.';
$string['locationoutside'] = 'Você está a aproximadamente  metros da referência da sala. O raio permitido é de  metros.';
$string['locationradius'] = 'Raio permitido (metros)';
$string['locationradius_help'] = 'Distância máxima entre a localização do navegador do aluno e a referência de sala registrada pelo professor.';
$string['locationrequired'] = 'A localização do navegador é obrigatória para enviar esta resposta.';
$string['locationunsupported'] = 'Este navegador não disponibiliza geolocalização.';
$string['modulename'] = 'Nível de confiança';
$string['modulename_help'] = 'Nível de confiança é uma atividade de metacognição e autoavaliação. O aluno escolhe entre quatro níveis: Não sei, Tenho pouca segurança, Acho que sei ou Domino. As respostas podem ser repetidas para comparar antes e depois da aula. A atividade pode ser anônima e pode restringir respostas pelo IP da sala do professor ou por geolocalização. Não atribui nota e não usa IA.';
$string['modulename_summary'] = 'Coleta uma autoavaliação rápida sobre a confiança do aluno em um conceito e compara a primeira resposta com a mais recente.';
$string['modulename_tip'] = 'Use a mesma atividade imediatamente antes e depois de uma aula quando quiser que o aluno reflita sobre a mudança na própria percepção de aprendizagem.';
$string['modulenameplural'] = 'Níveis de confiança';
$string['noinstances'] = 'Não há atividades Nível de confiança neste curso.';
$string['opensat'] = 'As respostas abrem em .';
$string['participant'] = 'Participante';
$string['participants'] = 'Participantes';
$string['pluginadministration'] = 'Administração do Nível de confiança';
$string['pluginname'] = 'Nível de confiança';
$string['presenceip'] = 'Mesmo IP do professor';
$string['presenceipnotice'] = 'Esta atividade exige o mesmo IP público da referência de sala registrada pelo professor.';
$string['presencelocation'] = 'Localização próxima da referência do professor';
$string['presencelocationnotice'] = 'Esta atividade exige localização do navegador em até  metros da referência de sala do professor.';
$string['presencenone'] = 'Sem validação de presença';
$string['presencevalidation'] = 'Validação de presença';
$string['presencevalidation_help'] = 'Opcionalmente exige que o aluno esteja no mesmo IP público da referência registrada pelo professor ou fisicamente próximo da localização registrada pelo professor.';
$string['privacy:metadata:confidence_reference'] = 'Armazena a referência de sala do professor para validação de presença.';
$string['privacy:metadata:confidence_reference:accuracy'] = 'Precisão informada pelo navegador para a localização de referência do professor.';
$string['privacy:metadata:confidence_reference:ipaddress'] = 'Endereço IP de referência do professor.';
$string['privacy:metadata:confidence_reference:latitude'] = 'Latitude de referência do professor.';
$string['privacy:metadata:confidence_reference:longitude'] = 'Longitude de referência do professor.';
$string['privacy:metadata:confidence_reference:timecreated'] = 'Momento em que a referência do professor foi criada.';
$string['privacy:metadata:confidence_reference:timemodified'] = 'Momento da última atualização da referência do professor.';
$string['privacy:metadata:confidence_reference:userid'] = 'Professor que registrou a referência.';
$string['privacy:metadata:confidence_response'] = 'Armazena o histórico das respostas de nível de confiança.';
$string['privacy:metadata:confidence_response:level'] = 'Nível de confiança selecionado.';
$string['privacy:metadata:confidence_response:participantkey'] = 'Chave pseudônima usada nas atividades anônimas.';
$string['privacy:metadata:confidence_response:timecreated'] = 'Momento em que a resposta foi criada.';
$string['privacy:metadata:confidence_response:timemodified'] = 'Momento da última modificação do registro de resposta.';
$string['privacy:metadata:confidence_response:userid'] = 'ID do usuário em atividades não anônimas.';
$string['question'] = 'Pergunta ou conteúdo';
$string['referenceip'] = 'IP de referência';
$string['referencelocation'] = 'Localização de referência';
$string['referencemissingteacher'] = 'Registre a referência da sala antes de os alunos responderem.';
$string['referencenotset'] = 'O professor ainda não registrou a referência da sala.';
$string['referencesaved'] = 'Referência da sala salva.';
$string['registerreference'] = 'Registrar referência da sala';
$string['reporttitle'] = 'Relatório de confiança: ';
$string['responsealreadyexists'] = 'Você já respondeu e esta atividade não permite alterar a resposta.';
$string['responsesaved'] = 'Seu nível de confiança foi salvo.';
$string['responsesettings'] = 'Configurações das respostas';
$string['savenewresponse'] = 'Salvar nova resposta';
$string['savingreference'] = 'Salvando referência da sala…';
$string['showresults'] = 'Alunos podem ver os resultados do grupo';
$string['showresultsafterclose'] = 'Após o encerramento da atividade';
$string['showresultsafterresponse'] = 'Após enviar a própria resposta';
$string['showresultsnever'] = 'Não podem ver';
$string['submissions'] = 'Respostas';
$string['submitresponse'] = 'Enviar resposta';
$string['teacherpanel'] = 'Controles do professor';
$string['timeclose'] = 'Encerrar em';
$string['timeopen'] = 'Abrir a partir de';
$string['unchanged'] = 'Mantiveram';
$string['unknownuser'] = 'Usuário desconhecido';
$string['viewreport'] = 'Abrir relatório em tempo real';
$string['yourconfidence'] = 'Qual é o seu nível de confiança agora?';
$string['yourcurrentresponse'] = 'Sua resposta atual:';
