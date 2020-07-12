/**
 * @copyright Copyright (c) 2020 Gonzalo Aguilar <gaguilar@level2crm.com>
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

(function() {
	OCA.WorkflowIFCConverter = OCA.WorkflowIFCConverter || {};

	/**
	 * @class OCA.WorkflowIFCConverter.Operation
	 */
	OCA.WorkflowIFCConverter.Operation =
		OCA.WorkflowEngine.Operation.extend({
			defaults: {
				'class': 'OCA\\WorkflowIFCConverter\\Operation',
				'name': '',
				'checks': [],
				'operation': ''
			}
		});

	/**
	 * @class OCA.WorkflowIFCConverter.OperationsCollection
	 *
	 * collection for all configured operations
	 */
	OCA.WorkflowIFCConverter.OperationsCollection =
		OCA.WorkflowEngine.OperationsCollection.extend({
			model: OCA.WorkflowIFCConverter.Operation
		});

	/**
	 * @class OCA.WorkflowIFCConverter.OperationView
	 *
	 * this creates the view for a single operation
	 */
	OCA.WorkflowIFCConverter.OperationView =
		OCA.WorkflowEngine.OperationView.extend({
			model: OCA.WorkflowIFCConverter.Operation,
			render: function() {
				var $el = OCA.WorkflowEngine.OperationView.prototype.render.apply(this);
				$el.find('input.operation-operation')
					.css('width', '400px')
					.select2({
						placeholder: t('workflow_ifc_converter', 'Mode…'),
						data: [
							{
								id: 'keep;preserve',
								text: t('workflow_ifc_converter', 'Keep original, preserve existing IFCs'),
							},
							{
								id: 'keep;overwrite',
								text: t('workflow_ifc_converter', 'Keep original, overwrite existing IFC'),
							},
							{
								id: 'delete;preserve',
								text: t('workflow_ifc_converter', 'Delete original, preserve existing IFCs'),
							},
							{
								id: 'delete;overwrite',
								text: t('workflow_ifc_converter', 'Delete original, overwrite existing IFC'),
							},
						],
					});
			}
		});

	/**
	 * @class OCA.WorkflowIFCConverter.OperationsView
	 *
	 * this creates the view for configured operations
	 */
	OCA.WorkflowIFCConverter.OperationsView =
		OCA.WorkflowEngine.OperationsView.extend({
			initialize: function() {
				OCA.WorkflowEngine.OperationsView.prototype.initialize.apply(this, [
					'OCA\\WorkflowIFCConverter\\Operation'
				]);
			},
			renderOperation: function(operation) {
				var subView = new OCA.WorkflowIFCConverter.OperationView({
					model: operation
				});

				OCA.WorkflowEngine.OperationsView.prototype.renderOperation.apply(this, [
					subView
				]);
			}
		});
})();


$(document).ready(function() {
	OC.SystemTags.collection.fetch({
		success: function() {
			new OCA.WorkflowIFCConverter.OperationsView({
				el: '#workflow_ifc_converter .rules',
				collection: new OCA.WorkflowIFCConverter.OperationsCollection()
			});
		}
	});
});
