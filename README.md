# mod_confidence - Nível de confiança

Atividade Moodle para metacognição e autoavaliação rápida. O professor publica uma pergunta ou conteúdo e o aluno informa um dos quatro níveis:

- Não sei
- Tenho pouca segurança
- Acho que sei
- Domino

O módulo mantém o histórico quando alterações são permitidas e compara automaticamente a primeira resposta com a resposta mais recente. Não atribui nota e não usa IA.

## Recursos

- respostas identificadas ou anônimas;
- possibilidade de responder novamente após a aula;
- comparação antes/depois;
- relatório em tempo real, atualizado a cada 3 segundos;
- troca instantânea entre gráfico de pizza, barras e área;
- professor decide se o aluno vê os resultados após responder, após o encerramento ou nunca;
- janela de abertura e encerramento;
- validação opcional de presença pelo mesmo IP público do professor;
- validação opcional por geolocalização dentro de um raio configurável;
- referência de sala registrada pelo professor na própria atividade;
- histórico completo de respostas;
- Privacy API;
- backup e restore Moodle 2.

## Anonimato

Quando o modo anônimo está ativo, o `userid` não é gravado na tabela de respostas. O plugin cria uma chave pseudônima HMAC por participante e por instância para permitir a comparação entre a primeira e a última resposta sem exibir a identidade no relatório.

## Presença

A validação por IP compara o IP público observado pelo Moodle para o professor e para o aluno. Em redes com NAT, vários dispositivos da mesma rede podem compartilhar o mesmo IP público, portanto esse recurso é uma confirmação de rede e não uma prova individual de presença.

Na validação por localização, o navegador precisa conceder permissão de geolocalização e normalmente deve estar em HTTPS. O servidor calcula a distância entre a localização do aluno e a referência registrada pelo professor e aplica o raio configurado. O IP e as coordenadas do aluno são usados apenas durante a validação e não são persistidos no histórico das respostas.

## Compatibilidade

Requer Moodle 4.5 ou superior.

## Instalação

O diretório do plugin no Moodle deve ser `mod/confidence`.

1. Extraia a pasta `confidence` dentro de `mod/`.
2. Acesse Administração do site > Notificações para concluir a instalação.
3. Para validação por geolocalização, publique o Moodle em HTTPS para que os navegadores possam fornecer localização com confiabilidade.

Após criar uma atividade com validação por IP ou localização, o professor deve abrir a atividade e usar **Registrar referência da sala** antes de liberar as respostas.
