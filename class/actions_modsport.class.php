<?php

/**
 * \file    class/actions_modsport.class.php
 * \ingroup modsport
 */

/**
 * Class ActionsModsport
 */
class ActionsModsport
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;
	/**
	 * @var string Error
	 */
	public $error = '';
	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}



	/**
	 * Overloading the addMoreActionsButtons function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$context = explode(':', $parameters['context']);
		if (in_array('productcard', $context) || in_array('ordercard', $context) || in_array('invoicecard', $context) )
		{

			/** @var CommonObject $object */
			$param = array(
				"attr" => array(
					"title" => "Test",
					'class' => 'classfortooltip'
				)
			);

			print dolGetButtonAction("$langs->trans('SessionCreate')", '<i class="fa fa-plus" aria-hidden="true"></i> Créer une session', 'default', dol_buildpath('/custom/modsport/sportactivities_card.php?fk_product='.$object->id.'&action=create&label='.substr($object->ref, 0, -2), 1), 'button-modsport-creation', $user->rights->modsport->sportactivities->write, $param );

		};
	}

}
