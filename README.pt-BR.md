# ST Template Control

[English](README.md) | **Português (Brasil)**

**Controla a vinculação persistente dos modelos no GLPI, permitindo a seleção nativa conforme a categoria, o perfil ativo e a entidade.**

O **ST Template Control** evita que o modelo gravado em um chamado, mudança ou problema continue sendo imposto na exibição apenas por estar vinculado ao registro. O plugin permite que o GLPI volte a selecionar o modelo conforme a categoria, o perfil ativo e a entidade, em um comportamento semelhante ao do GLPI 10.

**Versão: 2.0.0.**

**Autores: Service TIC (fork) / Curtis Conard**

Este pacote adota o nome e a descrição ST Template Control, sem alterar a lógica de tratamento dos modelos nem os valores padrão de configuração do pacote anterior do fork.

> Fork/adaptação da **Service TIC**, baseado no [Unstick ITIL Templates](https://github.com/cconard96/glpi-unsticktemplates-plugin), de **Curtis Conard**. Os créditos do projeto original e a licença GPL-3.0-or-later são preservados.

## Qual problema ele resolve?

No GLPI 11, o modelo utilizado na criação pode ficar associado ao registro e continuar influenciando sua exibição. Assim, campos ocultos pelo modelo utilizado no autoatendimento podem continuar ocultos quando um técnico abre o mesmo chamado. Esse cenário é descrito no [projeto original](https://github.com/cconard96/glpi-unsticktemplates-plugin).

O plugin retira esse vínculo gravado da posição de modelo forçado na exibição normal. Ele **não exibe todos os campos indiscriminadamente**: o modelo selecionado pelo contexto continua determinando a apresentação do formulário.

**Exemplo:** um solicitante utiliza um modelo simplificado e o técnico possui outro modelo no perfil ativo. Quando não há um modelo de categoria com precedência, o plugin permite que a exibição utilize o modelo do perfil do técnico, em vez de permanecer presa ao modelo gravado na abertura.

## Funcionalidades

| Recurso | Comportamento |
|---|---|
| Chamados, mudanças e problemas | Ativação independente para cada tipo de item. |
| Registros existentes | Neutraliza o modelo persistido durante a exibição normal, sem alterar o banco apenas ao abrir a página. |
| Novos registros | Remove o identificador persistente do modelo antes da inclusão; não desfaz os valores já aplicados no fluxo de criação. |
| Atualizações | Remove campos de vínculo do modelo recebidos na entrada e oferece limpeza do vínculo após a atualização. |
| Pré-visualização de modelos | Preserva uma pré-visualização solicitada explicitamente. |
| Administração | Tela de configuração, registro de depuração opcional e interface traduzida. |

O plugin utiliza pontos de extensão do GLPI; **não exige edição dos arquivos do núcleo**.

## Como o modelo é selecionado?

```text
Pré-visualização solicitada explicitamente
                ↓
Categoria / tipo do item
                ↓
Perfil ativo do usuário conectado
                ↓
Entidade
                ↓
Comportamento padrão do GLPI
```

Essa é uma visão resumida da resolução nativa. As regras internas sobre tipo, categoria, valores predefinidos e configuração da entidade continuam sendo executadas pelo GLPI. Consulte o [método de seleção no GLPI 11.0.11](https://github.com/glpi-project/glpi/blob/11.0.11/src/CommonITILObject.php#L7787-L7907).

**Não há uma ordem personalizada configurável.** O plugin também não cria uma origem independente de modelo no cadastro do usuário: “perfil ativo” é o perfil da sessão de quem está conectado, não necessariamente o perfil do solicitante do chamado.

O nome **Template Control** se refere ao controle da vinculação persistente dos modelos, não a um novo cálculo de prioridade, urgência, impacto ou SLA dos chamados.

## Requisitos

| Componente | Requisito declarado no plugin |
|---|---|
| GLPI | `>= 11.0.0` e `< 12.0.0` |
| PHP | `>= 8.2.0`, respeitando também os requisitos da versão instalada do GLPI |
| Diretório do plugin | `sttemplatecontrol` |
| Configurações | Permissão nativa `config`: leitura para consultar e atualização para salvar |

**Compatibilidade declarada não significa homologação em todas as versões 11.x.** Valide a versão exata do GLPI e as integrações do ambiente antes de produção. O pacote não se destina ao GLPI 10 nem declara suporte ao GLPI 12.

## Instalação

1. Faça backup e valide o plugin em homologação. Caso o fork anterior `sttemplatepriority` ou o plugin original `unsticktemplates` esteja ativo, desative-o antes de ativar este pacote; evite dois plugins tratando os mesmos vínculos simultaneamente.
2. Extraia a pasta `sttemplatecontrol` do pacote para o diretório `plugins` do GLPI. O caminho final deve ser `plugins/sttemplatecontrol/setup.php`, sem uma pasta intermediária.
3. Ajuste proprietário e permissões conforme a conta do servidor web e o padrão de segurança da instalação. Não utilize permissões `777`.
4. Acesse **Configurar > Plugins**, localize **ST Template Control**, instale e ative.
5. Abra **Configurar > Geral > ST Template Control**, revise as opções e salve.

Exemplo de extração, adaptando os caminhos ao servidor:

```bash
cd /usr/share/glpi/plugins
unzip /caminho/sttemplatecontrol-2.0.0.zip
```

Na instalação por Git, clone o **seu fork** com o nome local correto:

```bash
cd /usr/share/glpi/plugins
git clone URL_DO_SEU_FORK sttemplatecontrol
```

O pacote já inclui as traduções compiladas e não exige executar Composer para instalar este plugin. O GLPI deve estar previamente instalado e funcional.

### Substituição do fork com o nome anterior

O identificador interno agora é `sttemplatecontrol`. O pacote anterior `sttemplatepriority` possui outra identidade de plugin; esta substituição não é uma atualização ou redução de versão sobre o mesmo cadastro.

Antes da troca, faça backup do ambiente e registre as configurações anteriores. Desative `sttemplatepriority` (e `unsticktemplates`, quando aplicável) e instale a pasta completa `sttemplatecontrol` deste ZIP. **Não renomeie apenas a pasta antiga.**

As configurações de `plugin:sttemplatepriority` não são copiadas automaticamente para `plugin:sttemplatecontrol`. Revise e salve as seis opções no novo plugin antes de testar criação ou atualização de registros. As configurações antigas permanecem intactas. Não mantenha os dois plugins ativos simultaneamente.

A versão de distribuição é **2.0.0**, inclusive ao substituir um pacote local anteriormente preparado com outra numeração. A renomeação não restaura vínculos de modelos nem executa migração de dados em massa.

### Atualização de uma instalação ST Template Control

Faça backup da pasta e do banco, desative o plugin, substitua a pasta pelo novo pacote e siga a ação de atualização exibida pelo GLPI, quando houver. Depois, reative e valide as configurações. Não desinstale apenas para substituir os arquivos, pois a desinstalação remove as configurações do plugin.

Nas próximas atualizações com este nome, preserve a pasta e a chave `sttemplatecontrol`. Este pacote não altera o esquema do banco nem converte automaticamente o cadastro de nenhum dos plugins anteriores.

## Configurações e valores padrão

| Opção em português | Padrão | Finalidade |
|---|---|---|
| Ativar compatibilidade de modelos | Sim | Liga ou desliga a atuação do plugin. |
| Aplicar a chamados | Sim | Habilita o tratamento de chamados. |
| Aplicar a mudanças | Sim | Habilita o tratamento de mudanças. |
| Aplicar a problemas | Sim | Habilita o tratamento de problemas. |
| Limpar vínculo do modelo nas atualizações | Sim | Permite zerar o vínculo persistido após uma atualização. |
| Ativar registro de depuração | Não | Registra informações técnicas para diagnóstico. |

> **Atenção:** a limpeza do vínculo nas atualizações vem **ativada por padrão**. Para avaliar primeiro o efeito de exibição, desative essa opção antes de testar atualizações. Isso desativa a limpeza posterior realizada pelo plugin, mas não impede os demais tratamentos de entrada nem eventuais recálculos nativos do GLPI.

## Português e inglês

A interface acompanha o idioma escolhido nas **preferências do usuário no GLPI**; não há um seletor de idioma separado no plugin.

| Idioma | Catálogo |
|---|---|
| Português (Brasil) | `pt_BR` |
| English — United Kingdom | `en_GB` |
| English — United States | `en_US` |

Os catálogos de espanhol (`es_ES`) e francês (`fr_FR`) já existentes no anexo foram preservados. A versão em português usa “modelos” para os elementos da interface. O nome do produto e da empresa não é traduzido; os registros técnicos de depuração permanecem em inglês.

As traduções seguem o [padrão gettext do GLPI](https://glpi-developer-documentation.readthedocs.io/en/master/devapi/translations.html), com domínio `sttemplatecontrol`. Os arquivos `.po` são editáveis; os `.mo` são utilizados em execução. Ambos estão em `locales/`.

Para alterar uma tradução, edite o `.po` e recompile com GNU gettext:

```bash
msgfmt --check --check-format locales/pt_BR.po -o locales/pt_BR.mo
msgfmt --check --check-format locales/en_GB.po -o locales/en_GB.mo
msgfmt --check --check-format locales/en_US.po -o locales/en_US.mo
```

Preserve marcadores como `%s`. Caso uma tradução não apareça, confirme o idioma do usuário, a existência do `.mo` correspondente e faça novo login. Se a instalação mantiver catálogos antigos em cache, avalie a limpeza de cache ou recarga do serviço PHP conforme os procedimentos do ambiente.

## Impacto nos dados e reversão

O plugin **não faz atualização em massa** ao ser instalado ou ativado. As configurações são armazenadas no contexto nativo `plugin:sttemplatecontrol`, sem criação de tabela própria.

Na limpeza de uma atualização, o campo de vínculo do item pode ser definido como `0`: `tickettemplates_id`, `changetemplates_id` ou `problemtemplates_id`. Essa operação é distinta de excluir o cadastro do modelo.

Desativar o plugin interrompe sua atuação. Desinstalá-lo remove suas configurações, mas **não reconstrói vínculos já zerados nem os vínculos que deixaram de ser gravados em novos registros**. A reversão exata do estado anterior depende de backup e de um procedimento de restauração adequado.

Não há rotina de regravação em massa de categorias, títulos, descrições, anexos ou SLA. Entretanto, como a seleção do modelo influencia os campos apresentados e as regras do formulário, os fluxos de atendimento precisam ser homologados.

## Diagnóstico e validação

Com a depuração ativada, o caminho padrão do registro é:

```text
files/_log/sttemplatecontrol.log
```

O caminho pode variar se o diretório de logs do GLPI foi personalizado. Desative a depuração após o diagnóstico e utilize a política de retenção de logs do ambiente.

Para executar as verificações isoladas, com PHP, Python 3 e Bash disponíveis:

```bash
bash tests/static-check.sh
```

A suíte verifica sintaxe PHP, identidade do plugin e registro dos hooks, tratamento de modelos, configuração, catálogos de tradução e ativos locais do painel Service TIC. A validação das traduções verifica mensagens realmente presentes no `.mo`, consistência com o `.po` e integridade dos marcadores de formatação.

**Os testes isolados não substituem uma instância real do GLPI.** Homologue criação e edição de chamados, mudanças e problemas; perfis e entidades diferentes; campos ocultos e obrigatórios; troca de categoria; pré-visualização; formulários; API; coletor de e-mail e outros plugins utilizados no ambiente. Extensões que não passam pelos pontos de extensão utilizados podem apresentar comportamento diferente.

Consulte o [plano de testes](docs/PLANO_TESTES.md), a [análise técnica](docs/ANALISE_TECNICA.md), as [verificações desta revisão](docs/REVISAO_2.0.0.md) e o [histórico de alterações](CHANGELOG.md). Os documentos técnicos estão em português e o relatório de verificação do pacote é bilíngue. Os identificadores e caminhos foram atualizados para o novo nome.

## Origem, créditos e licença

O projeto original é **Unstick ITIL Templates**, de **Curtis Conard (`cconard96`)**. O ST Template Control amplia essa abordagem com tratamento da exibição, configuração por tipo de item, tratamento de atualizações, depuração e documentação multilíngue. A Service TIC é responsável por este fork/adaptação, não pela autoria exclusiva da ideia original.

Distribuído sob **GPL-3.0-or-later**. Consulte [LICENSE](LICENSE) e [NOTICE.md](NOTICE.md). Este é um plugin independente, não um componente oficial do núcleo do GLPI.

***
### Desenvolvido por: Service TIC Soluções Tecnológicas (Developed by: Service TIC Technological Solutions)

- [![E-mail](https://img.icons8.com/ios-filled/16/ffffff/mail.png)](mailto:contato@servicetic.com.br) **E-mail**: [contato@servicetic.com.br](mailto:contato@servicetic.com.br)
- [![Website](https://img.icons8.com/ios-filled/16/ffffff/domain.png)](https://www.servicetic.com.br) **Site**: [www.servicetic.com.br](https://www.servicetic.com.br)
- [![LinkedIn](https://img.icons8.com/ios-filled/16/ffffff/linkedin-circled.png)](https://www.linkedin.com/company/serviceticst) **LinkedIn**: [@serviceticst](https://www.linkedin.com/company/serviceticst)
- [![Instagram](https://img.icons8.com/ios-filled/16/ffffff/instagram-new.png)](https://www.instagram.com/serviceticst) **Instagram**: [@serviceticst](https://www.instagram.com/serviceticst)
- [![Facebook](https://img.icons8.com/ios-filled/16/ffffff/facebook-new.png)](https://www.facebook.com/serviceticst) **Facebook**: [@serviceticst](https://www.facebook.com/serviceticst)
- [![X](https://img.icons8.com/ios-filled/16/ffffff/x.png)](https://x.com/serviceticst) **X (Twitter)**: [@serviceticst](https://x.com/serviceticst)
- [![YouTube](https://img.icons8.com/ios-filled/16/ffffff/youtube-squared.png)](https://youtube.com/c/serviceticst) **YouTube**: [@serviceticst](https://youtube.com/c/serviceticst)
- [![WhatsApp](https://img.icons8.com/ios-filled/16/ffffff/whatsapp.png)](https://whatsapp.com/channel/0029VaAkV3P59PwXAiDepu3N) **Canal do WhatsApp**: [Clique aqui](https://whatsapp.com/channel/0029VaAkV3P59PwXAiDepu3N)

[![Service TIC](https://github.com/user-attachments/assets/17192a13-f0b6-4531-add0-99c7f46c24b0)](https://servicetic.com.br/links/)
