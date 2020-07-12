<template>
	<Multiselect :value="currentValue"
		:options="options"
		track-by="id"
		label="text"
		@input="(newValue) => newValue !== null && $emit('input', newValue.id)" />
</template>

<script>
import { Multiselect } from 'nextcloud-vue/dist/Components/Multiselect'

const ifcConvertOptions = [
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
]
export default {
	name: 'ConvertFromIFC',
	components: { Multiselect },
	props: {
		value: {
			default: ifcConvertOptions[0],
			type: String,
		},
	},
	data() {
		return {
			options: ifcConvertOptions,
		}
	},
	computed: {
		currentValue() {
			const newValue = ifcConvertOptions.find(option => option.id === this.value)
			if (typeof newValue === 'undefined') {
				return ifcConvertOptions[0]
			}
			return newValue
		},
	},
}
</script>

<style scoped>
	.multiselect {
		width: 100%;
		margin: auto;
		text-align: center;
	}
</style>
