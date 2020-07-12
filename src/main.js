import convertFromIFC from './ConvertFromIFC'

OCA.WorkflowEngine.registerOperator({
	id: 'OCA\\WorkflowIFCConverter\\Operation',
	operation: 'keep;preserve',
	options: convertFromIFC,
	color: '#dc5047',
})
