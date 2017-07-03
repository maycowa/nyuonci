<?php
/**
 * Constantes utilizadas no Nyu
 * 2016 Nyu Framework
 */

/*
 * Constantes utilizadas para criar regras de validação em NyuValidateRule
 */

/**
 * NyuValidateRule: $a maior que $b
 */
define("NYU_RULE_MORE", '$a > $b');

/**
 * NyuValidateRuleException: $a não é maior que $b
 */
define("NYU_RULE_MORE_EXCEPTION", "Valor menor que o esperado no campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a menor que $b
 */
define("NYU_RULE_LESS", '$a < $b');

/**
 * NyuValidateRuleException: $a não é menor a $b
 */
define("NYU_RULE_LESS_EXCEPTION", "Valor maior que o esperado no campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a maior ou igual a $b
 */
define("NYU_RULE_MORE_EQ", '$a >= $b');

/**
 * NyuValidateRuleException: $a não é maior a $b
 */
define("NYU_RULE_MORE_EQ_EXCEPTION", "Valor menor que o esperado no campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a menor ou igual ou igual a $b
 */
define("NYU_RULE_LESS_EQ", '$a <= $b');

/**
 * NyuValidateRuleException: $a não é menor ou igual a $b
 */
define("NYU_RULE_LESS_EQ_EXCEPTION", "Valor maior que o esperado no campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a igual a $b
 */
define("NYU_RULE_EQUAL", '$a == $b');

/**
 * NyuValidateRuleException: $a é diferente de $b
 */
define("NYU_RULE_EQUAL_EXCEPTION", "Valor diferente do esperado no campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a diferente de $b
 */
define("NYU_RULE_DIFF", '$a != $b');

/**
 * NyuValidateRuleException: $a igual a $b
 */
define("NYU_RULE_DIFF_EXCEPTION", "Valor diferente do esperado no campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a existe
 */
define("NYU_RULE_EXISTS", '$a != ""');

/**
 * NyuValidateRuleException: $a não existe
 */
define("NYU_RULE_EXISTS_EXCEPTION", "É necessário informar o valor do campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a possui no mínimo $b caracteres
 */
define("NYU_RULE_MIN_LEN", 'strlen($a) >= $b');

/**
 * NyuValidateRuleException: $a possui menos caracteres que o necessário
 */
define("NYU_RULE_MIN_LEN_EXCEPTION", "Quantidade de caracteres mínima não preenchida no campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a possui até $b caracteres
 */
define("NYU_RULE_MAX_LEN", 'strlen($a) <= $b');

/**
 * NyuValidateRuleException: $a possui mais caracteres que o necessário
 */
define("NYU_RULE_MAX_LEN_EXCEPTION", "Quantidade de caracteres máxima ultrapassada no campo \'#FIELD#\'.");

/**
 * NyuValidateRule: $a possui $b caracteres
 */
define("NYU_RULE_EQUAL_LEN", 'strlen($a) == $b');

/**
 * NyuValidateRuleException: $a possui mais ou menos caracteres que o necessário
 */
define("NYU_RULE_EQUAL_LEN_EXCEPTION", "Quantidade de caracteres diferente da necessária no campo \'#FIELD#\'.");

/*
 * Constantes utilizadas nas configurações do Nyu
 */

/**
 * NyuConfig: Indica que irá carregar uma configuração do tipo "routes"
 */
define("NYU_CONFIG_ROUTES", "routes");

/**
 * NyuConfig: Indica que irá carregar uma configuração do tipo "helpers"
 */
define("NYU_CONFIG_HELPERS", "helpers");

/**
 * NyuConfig: Indica que irá carregar uma configuração do tipo "database"
 */
define("NYU_CONFIG_DATABASE", "database");

/**
 * NyuConfig: Indica que irá carregar uma configuração do tipo "excep"
 */
define("NYU_CONFIG_EXCEP", "excep");

/**
 * NyuConfig: Indica que irá carregar uma configuração do tipo "mvc"
 */
define("NYU_CONFIG_MVC", "mvc");

/*
 * Constantes utilizadas para mensagens de erro em NyuBusiness
 */
/**
 * Regra de validação não informada
 */
define("NYU_BUSINESS_EXCEPTION_00", "Não foi informada uma regra de validação");