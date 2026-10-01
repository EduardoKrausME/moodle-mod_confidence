# mod_confidence - Nível de confiança

Atividade Moodle para metacognição e autoavaliação rápida. O professor publica uma pergunta ou conteúdo e o aluno informa
um dos quatro níveis:

- Não sei;
- Tenho pouca segurança;
- Acho que sei;
- Domino.

O módulo mantém o histórico quando alterações são permitidas e compara automaticamente a primeira resposta com a mais
recente. Não atribui nota e não usa IA.

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
- integração com a Privacy API;
- backup e restauração da atividade.

## Anonimato

Quando o modo anônimo está ativo, o `userid` não é gravado na tabela de respostas. O plugin cria uma chave pseudônima
HMAC por participante e por instância para permitir a comparação entre a primeira e a última resposta sem exibir a
identidade no relatório.

## Presença

A validação por IP compara o IP público observado pelo Moodle para o professor e para o aluno. Em redes com NAT, vários
dispositivos da mesma rede podem compartilhar o mesmo IP público, portanto esse recurso funciona como confirmação de
rede e não como prova individual de presença.

Na validação por localização, o navegador solicita permissão de geolocalização. O servidor calcula a distância entre a
posição do aluno e a referência registrada pelo professor e aplica o raio configurado. O IP e as coordenadas do aluno
são usados apenas durante a validação e não são persistidos no histórico das respostas.

Quando a atividade usa validação por IP ou localização, o professor abre a atividade e utiliza **Registrar referência da
sala** antes de liberar as respostas.
