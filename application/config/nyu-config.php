<?php
/**
 * Arquivo de configurações personalizadas do site
 *
 * NOTA:
 * Todas as configurações presentes neste arquivo podem ser configuradas via
 * json no arquivo sitedata/config.json, sobrescrevendo qualquer informação
 * preenchida neste arquivo de configuração. Alterações feitas através do
 * painel de configurações do Nyu - nyu:admin - grava neste arquivo json.
 * 
 * @package NyuConfig
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @since 3.1
 * @version 3.0
 */
/*
 * Informações básicas do sistema, para utilização em mensagens do sistema
 * $nyu__config['about']['site_name'] = Nome do Sistema legível
 * $nyu__config['about']['site_name_sys'] = Nome do Sistema interno, sem espaços 
 * e caracteres especiais
 * 
 */
$nyu__config['about']['site_name'] = "Nyu Framework";
$nyu__config['about']['site_name_sys'] = "nyuframework";

/* Configurações de miscelânea */
/**
 * Ignora página não encontrada
 * Se valor = 1, irá ignorar a tela de página não encontrada e irá carregar
 * a action index da controller index.
 * Se valor = 0, irá carregar a página de erro 404 ao não encontrar a página
 */
$nyu__config['misc']['ignore_not_found_page'] = 0;

/**
 * Administração do Nyu
 * Se valor = 1, permite acesso à administração do nyu através da url
 * SITE_URL.'_nyuadmin'
 */
$nyu__config['misc']['nyu_admin'] = 1;

/**
 * Cache de views
 * Se valor = 1, faz cache de todas as views através do twig. Armazena na pasta
 * de cache configurada em $nyu__config['mvc']['cache_path'] (abaixo)
 * Se valor = 0, não faz cache automático das views e carrega novamente a cada
 * requisição
 */
$nyu__config['misc']['cache_views'] = 0; 

/**
 * Arquivos de tratamento de erros de requisição, carregados automaticamente
 * em casos de erro, ou a partir do método NyuErrorManager::callErrorPage();
 */
$nyu__config['misc']['401_error_file'] = "/nyu/adminmvc/views/errorpages/401.php"; 
$nyu__config['misc']['403_error_file'] = "/nyu/adminmvc/views/errorpages/403.php"; 
$nyu__config['misc']['404_error_file'] = "/nyu/adminmvc/views/errorpages/404.php"; 
$nyu__config['misc']['500_error_file'] = "/nyu/adminmvc/views/errorpages/500.php"; 
$nyu__config['misc']['default_error_file'] = "/nyu/adminmvc/views/errorpages/default.php"; 

/* 
 * Rotas (tratamento para URL amigável)
 * 
 * Exemplo de utilização:
 * 
 * $nyu__config['routes']['url'] = 'foo/bar'
 * -> Acessando a url "exemplo.com/url", o sistema irá acessar a página 
 * "exemplo.com/foo/bar"
 * 
 * As declarações devem ser feitas do ultimo nível para o primeiro, pois são
 * executadas em cascata, como por exemplo:
 * $nyu__config['routes']['url/new'] = "foo";
 * $nyu__config['routes']['url'] = "bar";
 * 
 *  */
$nyu__config['routes'] = array();
$nyu__config['routes']['admin'] = "_nyuadmin/";

/*
 * Bibiotecas de funções (helpers) que serão carregadas automaticamente pelo 
 * sistema
 * Os arquivos devem ser criados na pasta "helpers" da raiz do sistema no 
 * formato de nome "nome.helper.php" e podem conter funções, variáveis e
 *  constantes ou qualquer outro código necessário.
 * Para a utilização e carregamento automático, inserir no array $nyu__helpers
 * como no exemplo:
 * 
 * helpers/foo.helper.php
 * helpers/bar.helper.php
 * 
 * $nyu__helpers = array('foo', 'bar');
 * 
 * Para carregamento manual (carregar as bibilotecas somente quando for 
 * necessário), utilizar a classe NyuHelperLoader
 */
$nyu__helpers = array();

/*
 * Aqui são descritos os bancos de dados que serão utilizados pela
 * aplicação
 * 
 * A configuração padrão do sistema encontra-se no índice 'default' - comentado
 * abaixo. Esta configuração será carregada automáticamente no sistema, sem 
 * necessidade de utilizar NyuCore::setDatabaseConfig() para alterar a 
 * configuração para a transação atual - Ver documentação dos métodos
 * NyuCore::setDatabaseConfig() e NyuCore::getDatabaseConfig() para maiores
 * explicações sobre multiconexão de banco de dados.
 * 
 * A estrutura esperada aqui é a seguinte:
 * 
 * Host do banco de dados (endereço pode ser endereço:porta ou apenas endereço)
 * $nyu__config['database']['nomedaconfiguracao']['host'] = "endereço";
 * Nome do banco de dados
 * $nyu__config['database']['nomedaconfiguracao']['name'] = "nomedobd";
 * Usuário do banco de dados
 * $nyu__config['database']['nomedaconfiguracao']['user'] = "usuariodobd";
 * Senha do banco de dados
 * $nyu__config['database']['nomedaconfiguracao']['password'] = 
 * "senhadousuariodobd";
 * Driver do banco de dados
 * $nyu__config['database']['nomedaconfiguracao']['driver'] = 
 * "mysql";
 * Caminho do arquivo de banco de dados (utilizado somente para sqlite; pode 
 * conter qualquer extensão de arquivo)
 * $nyu__config['database']['nomedaconfiguracao']['path'] = 
 * "/foo/bar/db.sqlite";
 * String de conexão do oracle
 * $nyu__config['database']['nomedaconfiguracao']['tns'] = "(DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 9999))
    )
    (CONNECT_DATA =
      (SERVICE_NAME = orcl)
    )
  )";
 * String de conexão customizada, para driver 'custom'
 * $nyu__config['database']['nomedaconfiguracao']['custom'] = 
 * 'configuracaocustomizada';
 * 
 * Se o índice 'driver' não for informado, o sistema utilizará mysql como padrão
 * Os valores esperados podem ser:
 *  - 'mysql':
 *      Cria a string do PDO no padrão mysql:dbname=db;host=127.0.0.1:9999
 *  - 'sqlite':
 *      Espera que exista a configuração 'path' com o caminho do arquivo e cria
 *      a string do PDO no padrão sqlite:/foo/bar/db.sqlite
 *  - 'mssql':
 *      Cria a string do PDO no padrão dblib:host=127.0.0.1:9999;dbname=db
 *  - 'oracle':
 *      Espera que exista a configuração 'tns' com a string de conexão, irá 
 *      criar a string de conexão no padrão oci:dbname=$tns
 *  - 'odbc':
 *      Espera que exista a configuração 'path' com o caminho do arquivo
 *  - 'custom':
 *      Espera que exista a configuração 'custom' com a string de conexão
 *      customizada
 */
$nyu__config['database'] = array();

$nyu__config['database']['default'] = array();
$nyu__config['database']['default']['host'] = "";
$nyu__config['database']['default']['name'] = "";
$nyu__config['database']['default']['user'] = "";
$nyu__config['database']['default']['password'] = "";

/*$nyu__config['database']['sqlite'] = array();
$nyu__config['database']['sqlite']['driver'] = 'sqlite';
$nyu__config['database']['sqlite']['path'] = __DIR__ ."/database.sqlite";*/

/* Configurações do padrão MVC */
/*
 * Em versões anteriores, o sistema utilizava o padrão de pastas
 * "mvc/public" para guardar os arquivos de mvc, em suas respectivas pastas:
 * "controller", "model" e "view". Caso houvesse necessidade de criar módulos,
 * como por exemplo, "usuario", era criada uma pasta dentro da pasta "modules"
 * com o nome "usuario", e dentro desta pasta, eram criadas as pastas padrão -
 * "controller, "model" e "view". A partir da versão 6.0, o sistema utiliza
 * um padrão de namespaces para módulos em controllers e models, alterando o 
 * padrão de pastas, removendo a necessidade de existirem as pastas "public" e 
 * "modules". Dessa forma, é possível utilizar controllers e models de quaisquer
 * módulos apenas chamando o namespace necessário e carregando o objeto.
 * Se houver necessidade de criar uma sub-pasta para o container mvc ou alterar 
 * o padrão de pastas para controllers, models e views, alterar nas variáveis 
 * abaixo.
 * 
 * NOTA: Para views, é possível alterar o caminho de pastas a carregar a partir
 * do método NyuTemplate::setPath(), passando o caminho completo de onde
 * os arquivos estão armazenados.
 */
/**
 * Pasta índice do mvc do sistema. Todas as outras pastas do mvc estão, por 
 * padrão, abaixo desta.
 * Alterar somente se necessário.
 */
$nyu__config['mvc']['index_folder'] = 'mvc';
/*
 * Sub-pasta padrão para os arquivos de MVC
 * - Deixar vazio para diretamente dentro da pasta MVC
 * - Inserir valor para identificar sub-pasta
 */
$nyu__config['mvc']['default_folder'] = '';

/**
 * Sub-pasta padrão para armazenar as controllers. É possível criar sub-pastas
 * com namespaces, se houver necessidade de uma maior organização ou separação 
 * por módulos. Por padrão, a pasta fica desta forma:
 * "mvc/controllers/"
 * 
 * Se houver necessidade de criar módulos, ficaria desta forma:
 * "mvc/controllers/foo/bar"
 * 
 * Uma controller "XyzController" com uma action "abcAction" dentro deste
 * módulo seria chamada na url desta forma:
 * 
 * echo SITE_URL . "foo/bar/xyz/abc/";
 * 
 * Se houver necessidade de alterar o nome - ou sub-caminho - da pasta de 
 * controllers, alterar nesta variável.
 */
$nyu__config['mvc']['controllers_folder'] = 'controllers';

/**
 * Sub-pasta padrão para armazenar as models. É possível criar sub-pastas
 * com namespaces, se houver necessidade de uma maior organização ou separação 
 * por módulos. Por padrão, a pasta fica desta forma:
 * "mvc/models/"
 * 
 * Se houver necessidade de criar módulos, ficaria desta forma:
 * "mvc/models/foo/bar"
 * 
 * A utilização de uma model "XyzModel" dentro do módulo ficaria então desta 
 * forma:
 * 
 * use \foo\bar as f;
 * $m = new f\XyzModel();
 * 
 * Se houver necessidade de alterar o nome - ou sub-caminho - da pasta de 
 * models, alterar nesta variável.
 */
$nyu__config['mvc']['models_folder'] = 'models';

/**
 * Sub-pasta padrão para armazenar as views. É possível criar sub-pastas para
 * organizar as views, sendo necessário apenas configurar no objeto 
 * NyuTemplate() a pasta onde serão carregadas as views, da seguinte forma:
 * 
 * $t = new NyuTemplate(array("template_dir" => "mvc/views/foo/bar/"));
 * $t->renderTemplate('template.twig');
 * 
 * Se houver necessidade de alterar o caminho do carregamento das views, é
 * necessário apenas chamar o método NyuTemplate::setPath():
 * 
 * $t->setPath('mvc/views/xyz/');
 * $t->renderTemplate('template2.twig');
 * 
 * É possível também informar o caminho para o template diretamente na chamada
 * de NyuTemplate::renderTemplate(), indo a partir do caminho padrão ou do 
 * caminho informado ao criar o objeto NyuTemplate:
 * 
 * $t = new NyuTemplate();
 * $t->renderTemplate('/foo/bar/template.twig');
 * 
 * Se houver necessidade de alterar o nome - ou sub-caminho - da pasta de 
 * views, alterar nesta variável.
 */
$nyu__config['mvc']['views_folder'] = 'views';

/**
 * Sub-pasta onde são armazenados os arquivos de cache do Twig
 */
$nyu__config['mvc']['cache_folder'] = 'cache';

/**
 * Para facilitar o acesso do caminho de controllers, models e views através
 * do método NyuConfig::getConfig(), existem as seguintes configurações:
 * controllers_path
 * models_path
 * views_path
 * 
 * Estas variáveis são utilizadas também internamente pelo sistema, altere-as
 * com cuidado e somente se necessário.
 */
/**
 * Caminho completo de controllers
 */
$nyu__config['mvc']['controllers_path'] = '/'.$nyu__config['mvc']['index_folder'].'/'.($nyu__config['mvc']['default_folder'] ? $nyu__config['mvc']['default_folder'].'/' : '').$nyu__config['mvc']['controllers_folder'].'/';

/**
 * Caminho completo de models
 */
$nyu__config['mvc']['models_path'] = '/'.$nyu__config['mvc']['index_folder'].'/'.($nyu__config['mvc']['default_folder'] ? $nyu__config['mvc']['default_folder'].'/' : '').$nyu__config['mvc']['models_folder'].'/';

/**
 * Caminho completo de views
 */
$nyu__config['mvc']['views_path'] = '/'.$nyu__config['mvc']['index_folder'].'/'.($nyu__config['mvc']['default_folder'] ? $nyu__config['mvc']['default_folder'].'/' : '').$nyu__config['mvc']['views_folder'].'/';

/**
 * Caminho completo de cache
 */
$nyu__config['mvc']['cache_path'] = '/'.$nyu__config['mvc']['index_folder'].'/'.$nyu__config['mvc']['cache_folder'].'/';

/**
 * Hack de processamento do Nyu
 * Esta variável indica o nome da classe que irá tratar dos hacks de 
 * processamento do Nyu.
 * A classe pode estar dentro de qualquer namespace, sendo necessário apenas 
 * informar o caminho completo da classe. A classe deve ser criada dentro
 * da pasta "siteclasses/classes", respeitando a estrutura de pastas do
 * namespace criado, se for utilizado um namespace. 
 * A classe de Hack deve implementar a interface NyuHackInteface, que contém
 * os métodos de hack possíveis.
 * O sistema irá criar um objeto do tipo da classe informada e irá carregar
 * os métodos de hack assim que for necessário.
 * Se não for utilizado nenhum hack, deixar a variável em branco.
 * Exemplos:
 * <code>
 * // Exemplo de criação da classe de hack
 * // arquivo "siteclasses/classes/foo/bar/MyHack.class.php"
 * namespace foo\bar;
 * 
 * class MyHack implements \NyuHackInterface{
 *     public function beforeLoadController(){
 *         echo "processamento antes da criação da controller";
 *     }
 * }
 * 
 * // Exemplo da variável $nyu__config['hack']
 * $nyu__config['hack'] = 'foo\bar\MyHack';
 * </code>
 * @since 6.0
 */
$nyu__config['hack'] = '';

/**
 * Pasta de bibliotecas externas do sistema (Ex: Twig)
 * A partir da versão 6.0, por padrão é 'vendor', para facilitar a utilização 
 * com Composer.
 */
$nyu__config['lib_folder'] = 'vendor';

/*
 * Debug dos erros de PHP
 * 0 - Desligado
 * 1 - Ligado
 */
$nyu__debug = 1;

/* Outras variáveis e constantes personalizadas do site */