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

namespace OCA\WorkflowIFCConverter;

use OCA\WorkflowEngine\Entity\File;
use OCA\WorkflowPDFConverter\BackgroundJobs\Convert;
use OCP\BackgroundJob\IJobList;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\GenericEvent;
use OCP\Files\Folder;
use OCP\Files\Node;
use OCP\IL10N;
use OCP\WorkflowEngine\IRuleMatcher;
use OCP\WorkflowEngine\ISpecificOperation;

class Operation implements ISpecificOperation {

	const MODES = [
		'keep;preserve',
		'keep;overwrite',
		'delete;preserve',
		'delete;overwrite',
	];

	/** @var IJobList */
	private $jobList;
	/** @var IL10N */
	private $l;

	public function __construct(IJobList $jobList, IL10N $l) {
		$this->jobList = $jobList;
		$this->l = $l;
	}

	/**
	 * @throws \UnexpectedValueException
	 * @since 9.1
	 */
	public function validateOperation(string $name, array $checks, string $operation): void {
		if(!in_array($operation, Operation::MODES)) {
			throw new \UnexpectedValueException($this->l->t('Please choose a mode.'));
		}
	}

	public function getDisplayName(): string {
		return $this->l->t('IFC conversion');
	}

	public function getDescription(): string {
		return $this->l->t('Convert documents from IFC format on upload and write.');
	}

	public function getIcon(): string {
		return \OC::$server->getURLGenerator()->imagePath('workflow_ifc_converter', 'app.svg');
	}

	public function isAvailableForScope(int $scope): bool {
		return true;
	}

	public function onEvent(string $eventName, Event $event, IRuleMatcher $ruleMatcher): void {
		if(!$event instanceof GenericEvent) {
			return;
		}
		try {
			if($eventName === '\OCP\Files::postRename') {
				/** @var Node $oldNode */
				list(, $node) = $event->getSubject();
			} else {
				$node = $event->getSubject();
			}
			/** @var Node $node */

			// '', admin, 'files', 'path/to/file.txt'
			list(,, $folder,) = explode('/', $node->getPath(), 4);
			if($folder !== 'files' || $node instanceof Folder) {
				return;
			}

			// avoid converting xkts into xkts - would become infinite
			// also some types we know would not succeed
			if($node->getMimetype() === 'model/xkt-binary'
				|| $node->getMimePart() === 'video'
				|| $node->getMimePart() === 'audio'
			) {
				return;
			}

			$matches = $ruleMatcher->getFlows(false);
			$originalFileMode = $targetIFCMode = null;
			foreach($matches as $match) {
				$fileModes = explode(';', $match['operation']);
				if($originalFileMode !== 'keep') {
					$originalFileMode = $fileModes[0];
				}
				if($targetIFCMode !== 'preserve') {
					$targetIFCMode = $fileModes[1];
				}
				if($originalFileMode === 'keep' && $targetIFCMode === 'preserve') {
					// most conservative setting, no need to look into other modes
					break;
				}
			}
			if(!empty($originalFileMode) && !empty($targetIFCMode)) {
				$this->jobList->add(Convert::class, [
					'path' => $node->getPath(),
					'originalFileMode' => $originalFileMode,
					'targetIFCMode' => $targetIFCMode,
				]);
			}
		} catch(\OCP\Files\NotFoundException $e) {
		}
	}

	public function getEntityId(): string {
		return File::class;
	}
}
