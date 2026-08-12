import RepeaterRowView from './fields-repeater-row';

export default class extends elementor.modules.controls.Repeater {
	className() {
		let classes = super.className();

		classes += ' elementor-control-type-repeater';

		return classes;
	}

	getChildView() {
		return RepeaterRowView;
	}
}
