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
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Overloading the addMoreActionsButtons function : replacing the parent's function with the one below
	 *
	 * @param array()         $parameters     Hook metadatas (context, etc...)
	 * @param CommonObject $object      The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param string       $action      Current action (if set). Generally create or edit or null
	 * @param HookManager  $hookmanager Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$context = explode(':', $parameters['context']);
		if (in_array('productcard', $context) || in_array('ordercard', $context) || in_array('invoicecard', $context)) {

			/** @var CommonObject $object */
			$param = array(
				"attr" => array(
					"title" => "Test",
					'class' => 'classfortooltip'
				)
			);

			// On stocke l'unité de valeur de la durée (dans la BDD, "i" == "minutes" et "h" == heure)
			$mesure = substr($object->duration, -1) == 'i' ? 'minutes' : substr($object->duration, -1);

			print dolGetButtonAction("$langs->trans('SessionCreate')", '<i class="fa fa-plus" aria-hidden="true"></i> Créer une session', 'default',
				dol_buildpath('/custom/modsport/sportactivities_card.php?fk_product='
					. $object->id . "&date_debut=" . date("Y-m-d H:i:s") . "&date_fin="
					. date('Y-m-d H:i:s', strtotime('+' . substr($object->duration, 0, strlen($object->duration - 1)) . ' ' . $mesure)),
					1)
				. "&action=create&label="
				. substr($object->ref, 0, -2), 'button-modsport-creation', $user->rights->modsport->sportactivities->write, $param);

			$form = new Form($this->db);
			if ($action == 'ask_delete_modsportchild') {
				print $form->formconfirm($_SERVER["PHP_SELF"]
					. '?id=' . $object->id, $langs->trans('modSport_doublecheck_delete')
					, $langs->trans('modSport_CheckConfirmDeleteProduct', $object->ref), 'confirm_delete_double'
					, 'yes', 'action-delete', 350, 300);
			}
		};
	}

	public function formObjectOptions($parameters, &$object, &$action, $idUser)
	{
		global $db;

		$form = new Form($db);
		$sql = "SELECT COUNT(*) as c FROM " . MAIN_DB_PREFIX . "modsport_sportactivities WHERE fk_product = " . $object->id;
		$resql = $this->db->query($sql);

		if ($resql) {
			if ($resql > 0) {
				$obj = $this->db->fetch_object($resql);

				print '<td>';
				print 'Nombre de sessions liées à ce service';
				print '</td>';
				print '<td>';
				print $obj->c;
				print '</td>';
			};
		}
	}

	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf, $user, $mc;
		$form = new Form($this->db);

		if ($action == 'confirm_delete') {
			$sql = "SELECT COUNT(*) as c FROM " . MAIN_DB_PREFIX . "modsport_sportactivities WHERE fk_product =" . $object->id;
			$resql = $this->db->query($sql);

			if ($resql) {
				if ($resql > 0) {
					$obj = $this->db->fetch_object($resql);
					if ($obj->c > 0) {
						setEventMessage("Il y a encore " . $obj->c . " session(s) liées à votre produit");
						header('location: ' . DOL_URL_ROOT . '/product/card.php?id=' . $object->id . '&action=ask_delete_modsportchild');
						exit;
					}
				};
			}
		}

		if ($action == 'confirm_delete_double') {
			$sql = 'SELECT rowid FROM ' . MAIN_DB_PREFIX . 'modsport_sportactivities WHERE fk_product = ' . $object->id;

			$resql = $this->db->query($sql);
			if ($resql) {
				while ($obj = $this->db->fetch_object($resql)) {
					$sqldelete = 'DELETE FROM ' . MAIN_DB_PREFIX . 'modsport_sportactivities WHERE rowid = '. $obj->rowid;
					$this->db->query($sqldelete);
				}
			}
			$action = 'confirm_delete';
			return 0;
		}
	}
}


