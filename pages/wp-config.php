<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */
define('FS_METHOD', 'ssh2');
// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'bet188_wp' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'bet188_wpuser' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', 't5Z8DTRpqNk7' );

/** Nome do host do MySQL */
define( 'DB_HOST', 'localhost' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'J(Qf74^5M-]ye]}1P6A-Xu$GDBM]G|Ba3*ScOG[gJn^PNw$,Y=LWc7:.fch4$Dws' );
define( 'SECURE_AUTH_KEY',  '#z]DV#C#Z:N^n{g=};WX3UU8POacjn_qNQt4Cu:[p[*;zV;ac6$RawiA$XLtm[!s' );
define( 'LOGGED_IN_KEY',    'T`0B|()@n:N#6sgaYp%,;AJK<t.y;4qtk2~jU8SePA|9LzD@K@]neEt8|HzrEO3B' );
define( 'NONCE_KEY',        '4:#s#h}#^rI#?cqbrUQ+nh`!z9~O&U-=0|3cIh?8(a$AYYjvCl*!Pz1e]uM+qHe7' );
define( 'AUTH_SALT',        '^n93kdM850:2aYtHSm x]H1~*>ov)xRF9CTqI*+TC$v}n[VK;ZYkX:10qrf_@`.G' );
define( 'SECURE_AUTH_SALT', '_@xBrF+{lk3; ns%?kF=L])+/B..u9mh]T9T*4=L<_;22z~*bMf[V<`o^3h,6uzZ' );
define( 'LOGGED_IN_SALT',   '.QvN6LwJ&b?BM@a;Np#z!*J@U4Aa(4@y[rQeyNDXCe9,~zeE_:8RL0pBpi603(D5' );
define( 'NONCE_SALT',       'C8++}&^TxRwS|;D0yL&J-7B,7o!sPE[-b>5.dLW:0C m15BHqve<3h>}3~vd,P-6' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'bet_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
