<?php
/**
 * @copyright Copyright (c) 2020 Gonzalo Aguilar Delgado <gaguilar@level2crm.com>
 *
 * @author Gonzalo Aguilar Delgado <gaguilar@level2crm.com>
 * 
 * Derived from: https://github.com/nextcloud/workflow_pdf_converter
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\WorkflowIFCConverter\AppInfo;

use OCA\WorkflowIFCConverter\Operation;
use OCP\WorkflowEngine\IManager;
use Symfony\Component\EventDispatcher\GenericEvent;

class Application extends \OCP\AppFramework\App {

	/**
	 * Application constructor.
	 */
	public function __construct() {
		parent::__construct('workflow_ifc_converter');
		\OC::$server->getEventDispatcher()->addListener(IManager::EVENT_NAME_REG_OPERATION, function (GenericEvent $event) {
			$operation = \OC::$server->query(Operation::class);
			$event->getSubject()->registerOperation($operation);
			\OC_Util::addScript('workflow_ifc_converter', 'workflow_ifc_converter');
		});
	}

}
