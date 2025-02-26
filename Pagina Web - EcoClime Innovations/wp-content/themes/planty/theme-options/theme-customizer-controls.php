<?php
/**
 * Theme customizer: Custom controls
 *
 * @package PLANTY
 * @since PLANTY 1.0.31
 */


/**
 * Class Planty_Customize_Theme_Control.
 *
 * A base class to create all theme-specific controls for Customizer.
 *
 * Extends class WP_Customize_Control.
 */
class Planty_Customize_Theme_Control extends WP_Customize_Control {
	
	protected function start_render_field() {
		?>
		<div class="customize-control-wrap<?php
			if ( ! empty( $this->input_attrs['data-pro-only'] ) ) {
				echo ' planty_options_pro_only';
			}
		?>">
		<?php
	}

	protected function end_render_field() {
		if ( ! empty( $this->input_attrs['data-pro-only'] ) ) {
			planty_show_layout( planty_add_inherit_cover( $this->id, array( 'type' => $this->type, 'pro_only' => true ) ) );
		}
		?>
		</div>
		<?php
	}

	protected function render_field_title() {
		if ( ! empty( $this->label ) ) {
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php
		}
	}

	protected function render_field_description() {
		if ( ! empty( $this->description ) ) {
			?>
			<span class="customize-control-description description"><?php planty_show_layout( $this->description ); ?></span>
			<?php
		}
	}

	protected function render_content() {
		$this->start_render_field();
		if ( ! empty( $this->input_attrs['data-pro-only'] ) && 'checkbox' == $this->type ) {
			$this->render_field_title();
			$this->render_field_description();
		}
		parent::render_content();
		$this->end_render_field();
	}

}


// 'info' field
//--------------------------------------------------------------------
class Planty_Customize_Info_Control extends Planty_Customize_Theme_Control {
	public $type = 'info';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		$this->end_render_field();
	}
}


// 'hidden' field
//--------------------------------------------------------------------
class Planty_Customize_Hidden_Control extends Planty_Customize_Theme_Control {
	public $type = 'hidden';

	protected function render_content() {
		?>
		<input type="hidden" name="_customize-hidden-<?php echo esc_attr( $this->id ); ?>" value=""
			<?php
			$this->link();
			if ( ! empty( $this->input_attrs['var_name'] ) ) {
				echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
			}
			?>
		>
		<?php
		// We need to fire action 'admin_print_footer_scripts' if this is a last option
		if ( 'last_option' == $this->id && planty_storage_get( 'need_footer_scripts', false ) ) {
			planty_storage_set( 'need_footer_scripts', false );
			do_action( 'admin_print_footer_scripts' );
		}
	}
}


// 'button' field
//--------------------------------------------------------------------
class Planty_Customize_Button_Control extends Planty_Customize_Theme_Control {
	public $type = 'button';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		if ( ! empty( $this->input_attrs['link'] ) ) {
			?>
			<a href="<?php echo esc_url( $this->input_attrs['link'] ); ?>" target="_blank"
				<?php
				if ( ! empty( $this->input_attrs['class'] ) ) {
					echo ' class="' . esc_attr( $this->input_attrs['class'] ) . '"';
				}
				?>
			>
				<?php
				echo esc_html( $this->input_attrs['caption'] );
				?>
			</a>
			<?php
		} elseif ( ! empty( $this->input_attrs['action'] ) ) {
			?>
			<input type="button" 
				<?php
				if ( ! empty( $this->input_attrs['class'] ) ) {
					echo ' class="' . esc_attr( $this->input_attrs['class'] ) . '"';
				}
				?>
				name="_customize-button-<?php echo esc_attr( $this->id ); ?>" 
				value="<?php echo esc_attr( $this->input_attrs['caption'] ); ?>"
				data-action="<?php echo esc_attr( $this->input_attrs['action'] ); ?>"
			>
			<?php
		}
		$this->end_render_field();
	}
}


// 'switch' field
//--------------------------------------------------------------------
class Planty_Customize_Switch_Control extends Planty_Customize_Theme_Control {
	public $type = 'switch';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		?>
		<label class="customize-control-field-wrap planty_options_item_switch">
			<input type="hidden"
				<?php
				$this->link();
				if ( ! empty( $this->input_attrs['var_name'] ) ) {
					echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
				}
				?>
				value="<?php
					if ( ! empty( $this->input_attrs['value'] ) ) {
						echo esc_attr( $this->input_attrs['value'] );
					}
					?>"
			/>
			<input type="checkbox" value="1" <?php
				if ( ! empty( $this->input_attrs['value'] ) ) {
					?> checked="checked"<?php
				}
				?>
			/>
			<span class="planty_options_item_holder" tabindex="0">
				<span class="planty_options_item_holder_back"></span>
				<span class="planty_options_item_holder_handle"></span>
			</span>
			<?php if ( ! empty( $this->label ) ) { ?>
				<span class="planty_options_item_caption">
					<?php echo esc_html( $this->label ); ?>
				</span>
			<?php } ?>
		</label>
		<?php
		$this->end_render_field();
	}
}


// 'icon' field
//--------------------------------------------------------------------
class Planty_Customize_Icon_Control extends Planty_Customize_Theme_Control {
	public $type = 'icon';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		?>
		<span class="customize-control-field-wrap"><input type="text" 
			<?php
			$this->link();
			if ( ! empty( $this->input_attrs['var_name'] ) ) {
				echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
			}
			?>
		/>
			<?php
			planty_show_layout(
				planty_show_custom_field(
					'_customize-icon-selector-' . esc_attr( $this->id ),
					array(
						'type'   => 'icons',
						'button' => true,
						'icons'  => true,
					),
					$this->input_attrs['value']
				)
			);
			?>
		</span>
		<?php
		$this->end_render_field();
	}
}


// 'checklist' field
//--------------------------------------------------------------------
class Planty_Customize_Checklist_Control extends Planty_Customize_Theme_Control {
	public $type = 'checklist';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		?>
		<span class="customize-control-field-wrap"><input type="hidden" 
			<?php
			$this->link();
			if ( ! empty( $this->input_attrs['var_name'] ) ) {
				echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
			}
			?>
		/>
			<?php
			planty_show_layout(
				planty_show_custom_field(
					'_customize-checklist-' . esc_attr( $this->id ),
					array_merge(
						$this->input_attrs, array(
							'options' => $this->choices,
						)
					),
					$this->input_attrs['value']
				)
			);
			?>
		</span>
		<?php
		$this->end_render_field();
	}
}


// 'choice' field
//--------------------------------------------------------------------
class Planty_Customize_Choice_Control extends Planty_Customize_Theme_Control {
	public $type = 'choice';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		?>
		<span class="customize-control-field-wrap"><input type="hidden" 
			<?php
			$this->link();
			if ( ! empty( $this->input_attrs['var_name'] ) ) {
				echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
			}
			?>
		/>
			<?php
			planty_show_layout(
				planty_show_custom_field(
					'_customize-choice-' . esc_attr( $this->id ),
					array_merge(
						$this->input_attrs, array(
							'options' => $this->choices,
						)
					),
					$this->input_attrs['value']
				)
			);
			?>
		</span>
		<?php
		$this->end_render_field();
	}
}


// 'scheme_editor' field
//--------------------------------------------------------------------
class Planty_Customize_Scheme_Editor_Control extends Planty_Customize_Theme_Control {
	public $type = 'scheme_editor';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		?>
		<span class="customize-control-field-wrap"><input type="hidden" 
			<?php
			$this->link();
			if ( ! empty( $this->input_attrs['var_name'] ) ) {
				echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
			}
			?>
		/>
			<?php
			planty_show_layout(
				planty_show_custom_field(
					'_customize-scheme-editor-' . esc_attr( $this->id ),
					$this->input_attrs,
					planty_unserialize( $this->input_attrs['value'] )
				)
			);
			?>
		</span>
		<?php
		$this->end_render_field();
	}
}


// 'text_editor' field
//--------------------------------------------------------------------
class Planty_Customize_Text_Editor_Control extends Planty_Customize_Theme_Control {
	public $type = 'text_editor';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		?>
		<span class="customize-control-field-wrap"><input type="hidden" 
			<?php
			$this->link();
			if ( ! empty( $this->input_attrs['var_name'] ) ) {
				echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
			}
			?>
			value="<?php echo esc_textarea( $this->value() ); ?>"
		/>
			<?php
			planty_show_layout(
				planty_show_custom_field(
					'_customize-text-editor-' . esc_attr( $this->id ),
					$this->input_attrs,
					$this->input_attrs['value']
				)
			);
			?>
		</span>
		<?php
		$this->end_render_field();
		// We need to fire action 'admin_print_footer_scripts' when the last option is render
		planty_storage_set( 'need_footer_scripts', true );
	}
}



// 'range' field
//--------------------------------------------------------------------
class Planty_Customize_Range_Control extends Planty_Customize_Theme_Control {
	public $type = 'range';

	protected function render_content() {
		$this->start_render_field();
		$this->render_field_title();
		$this->render_field_description();
		$show_value = ! isset( $this->input_attrs['show_value'] ) || $this->input_attrs['show_value'];
		?>
		<span class="customize-control-field-wrap"><input type="<?php echo ! $show_value ? 'hidden' : 'text'; ?>" 
			<?php
			$this->link();
			if ( $show_value ) {
				echo ' class="planty_range_slider_value"';
			}
			if ( ! empty( $this->input_attrs['var_name'] ) ) {
				echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
			}
			?>
		/>
			<?php
			planty_show_layout(
				planty_show_custom_field(
					'_customize-range-' . esc_attr( $this->id ),
					$this->input_attrs,
					$this->input_attrs['value']
				)
			);
			?>
		</span>
		<?php
		$this->end_render_field();
	}
}
