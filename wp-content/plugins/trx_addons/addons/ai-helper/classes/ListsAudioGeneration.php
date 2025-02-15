<?php
namespace TrxAddons\AiHelper;

if ( ! trait_exists( 'ListsAudioGeneration' ) ) {

	/**
	 * Return arrays with the lists used for the Audio generation
	 */
	trait ListsAudioGeneration {

		/**
		 * Return a list of audio generation APIs
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of audio generation APIs
		 */
		static function get_list_ai_audio_apis() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_audio_apis', array(
				'openai' => esc_html__( 'Open AI', 'trx_addons' ),
				'modelslab' => esc_html__( 'ModelsLab', 'trx_addons' ),
			) );
		}

		/**
		 * Return a default list of audio APIs with the enabled status
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The default list of audio APIs
		 */
		static function get_list_ai_audio_apis_enabled() {
			$api_list = self::get_list_ai_audio_apis();
			if ( ! is_array( $api_list ) ) {
				$api_list = array();
			}
			foreach( $api_list as $api => $title ) {
				$api_list[ $api ] = 1;
			}
			return $api_list;
		}

		/**
		 * Return a list of audio models for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of audio models
		 */
		static function get_list_ai_audio_models( $type = 'tts', $groups = true ) {
			$api_order = trx_addons_get_option( 'ai_helper_sc_agenerator_api_order', self::get_list_ai_audio_apis_enabled() );
			$models = array();
			foreach( $api_order as $api => $enable ) {
				// ModelsLab
				if ( $api == 'modelslab' && (int)$enable > 0 ) {
					$ml_models = self::get_modelslab_audio_models( $type );
					if ( is_array( $ml_models ) && count( $ml_models ) > 0 ) {
						if ( $groups ) {
							$models[ 'modelslab/-' ] = '\\-' . __( 'ModelsLab models', 'trx_addons' );
						}
						foreach ( $ml_models as $k => $v ) {
							$models[ 'modelslab/' . $k ] = $v['title'];
						}
					}

				// Open AI
				} else if ( $api == 'openai' && (int)$enable > 0 ) {
					$openai_models = self::get_openai_audio_models( $type );
					if ( is_array( $openai_models ) && count( $openai_models ) > 0 ) {
						if ( $groups ) {
							$models[ 'openai/-' ] = '\\-' . __( 'Open AI models', 'trx_addons' );
						}
						foreach ( $openai_models as $k => $v ) {
							$models[ 'openai/' . $k ] = $v['title'];
						}
					}
				}
			}
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_audio_models', $models, $type );
		}

		/**
		 * Return a list of audio generator layouts
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image generator layouts
		 */
		static function get_list_sc_audio_generator_layouts() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_sc_audio_generator_layouts', array(
				'default'  => esc_html__( 'Default', 'trx_addons' ),
			) );
		}



		/* OPEN AI API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a default list of audio models for Open AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @param string $type The type of models (tts|transcription|translation)
		 * 
		 * @return array  	  The list of audio models for Open AI
		 */
		static function get_default_openai_audio_models( $type = 'tts' ) {
			$list = array();
			if ( $type == 'tts' ) {
				$list = array(
					'tts-1' => array(
						'title' => esc_html__( 'Text To Speech', 'trx_addons' )
					),
					'tts-1-hd' => array(
						'title' => esc_html__( 'Text To Speech HD', 'trx_addons' )
					),
				);
			} else if ( $type == 'transcription' ) {
				$list = array(
					'whisper-1' => array(
						'title' => esc_html__( 'Whisper', 'trx_addons' )
					),
				);
			} else if ( $type == 'translation' ) {
				$list = array(
					'whisper-1' => array(
						'title' => esc_html__( 'Whisper', 'trx_addons' )
					),
				);
			}
			return apply_filters( 'trx_addons_filter_ai_helper_default_openai_audio_models', $list, $type );
		}

		/**
		 * Return a list of audio models for Open AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @param string $type The type of models (tts|transcription|translation)
		 * 
		 * @return array  	  The list of audio models for Open AI
		 */
		static function get_openai_audio_models( $type = 'tts' ) {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_openai', '' );
			if ( ! empty( $token ) ) {
				$models = trx_addons_get_option( 'ai_helper_' . str_replace( '-', '_', $type ) . '_models_openai', false );
				if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
					$models = self::get_default_openai_audio_models( $type);
				} else {
					$new_models = array();
					foreach ( $models as $k => $v ) {
						if ( ! empty( $v['id'] ) ) {
							$new_models[ $v['id'] ] = $v;
							unset( $new_models[ $v['id'] ]['id'] );
						}
					}
					$models = $new_models;
				}
			}

			return $models;
		}

		/**
		 * Return a list of voices for Open AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of voices for Open AI
		 */
		static function get_list_openai_voices() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_openai_voices', array(
				'alloy'   => esc_html__( 'Alloy', 'trx_addons' ),
				'echo'    => esc_html__( 'Echo', 'trx_addons' ),
				'fable'   => esc_html__( 'Fable', 'trx_addons' ),
				'onyx'    => esc_html__( 'Onyx', 'trx_addons' ),
				'nova'    => esc_html__( 'Nova', 'trx_addons' ),
				'shimmer' => esc_html__( 'Shimmer', 'trx_addons' ),
			) );
		}



		/* MODELSLAB API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a default list of audio models for ModelsLab
		 * 
		 * @access public
		 * @static
		 * 
		 * @param string $type The type of models (tts|transcription|voice-cover)
		 * 
		 * @return array  	  The list of audio models for ModelsLab
		 */
		static function get_default_modelslab_audio_models( $type = 'tts' ) {
			$list = array();
			if ( $type == 'tts' ) {
				$list = array(
					'text-to-audio' => array(
						'title' => esc_html__( 'Text to Audio', 'trx_addons' )
					),
				);
			} else if ( $type == 'transcription' ) {
				$list = array(
					'speech-to-text' => array(
						'title' => esc_html__( 'Speech to Text', 'trx_addons' )
					),
				);
			} else if ( $type == 'voice-cover' ) {
				$list = array(
					'voice-cover' => array(
						'title' => esc_html__( 'Voice Cover', 'trx_addons' )
					),
				);
			}
			return apply_filters( 'trx_addons_filter_ai_helper_default_modelslab_audio_models', $list, $type );
		}

		/**
		 * Return a list of audio models for ModelsLab
		 * 
		 * @access public
		 * @static
		 * 
		 * @param string $type The type of models (tts|transcription|voice-cover)
		 * 
		 * @return array  	  The list of audio models for ModelsLab
		 */
		static function get_modelslab_audio_models( $type = 'tts' ) {
			$models = array();
			$token = trx_addons_get_option( 'ai_helper_token_modelslab', '' );
			if ( ! empty( $token ) ) {
				$models = trx_addons_get_option( 'ai_helper_' . str_replace( '-', '_', $type ) . '_models_modelslab', false );
				if ( empty( $models ) || ! is_array( $models ) || empty( $models[0]['id'] ) ) {
					$models = self::get_default_modelslab_audio_models( $type);
				} else {
					$new_models = array();
					foreach ( $models as $k => $v ) {
						if ( ! empty( $v['id'] ) ) {
							$new_models[ $v['id'] ] = $v;
							unset( $new_models[ $v['id'] ]['id'] );
						}
					}
					$models = $new_models;
				}
			}

			return $models;
		}

		/**
		 * Return a list of languages for ModelsLab
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of languages for ModelsLab
		 */
		static function get_list_modelslab_languages() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_modelslab_languages', array(
				''		    => esc_html__( '- Auto -','trx_addons' ),
				'english'   => esc_html__( 'English','trx_addons' ),
				'arabic'    => esc_html__( 'Arabic','trx_addons' ),
				'spanish'   => esc_html__( 'Spanish','trx_addons' ),
				'german'    => esc_html__( 'German','trx_addons' ),
				'czech'     => esc_html__( 'Czech','trx_addons' ),
				'chinese'   => esc_html__( 'Chinese','trx_addons' ),
				'dutch'     => esc_html__( 'Dutch','trx_addons' ),
				'french'    => esc_html__( 'French','trx_addons' ),
				'hindi'     => esc_html__( 'Hindi','trx_addons' ),
				'hungarian' => esc_html__( 'Hungarian','trx_addons' ),
				'italian'   => esc_html__( 'Italian','trx_addons' ),
				'japanese'  => esc_html__( 'Japanese','trx_addons' ),
				'korean'    => esc_html__( 'Korean','trx_addons' ),
				'polish'    => esc_html__( 'Polish','trx_addons' ),
				'russian'   => esc_html__( 'Russian','trx_addons' ),
				'turkish'   => esc_html__( 'Turkish','trx_addons' ),
			) );
		}

		/**
		 * Return a list of emotions for ModelsLab
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of emotions for ModelsLab
		 */
		static function get_list_modelslab_emotions() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_modelslab_emotions', array(
				'neutral' => esc_html__( 'Neutral','trx_addons' ),
				'happy'   => esc_html__( 'Happy','trx_addons' ),
				'sad'     => esc_html__( 'Sad','trx_addons' ),
				'angry'   => esc_html__( 'Angry','trx_addons' ),
				'dull'    => esc_html__( 'Dull','trx_addons' ),
			) );
		}

		/**
		 * Return a list of voices for ModelsLab
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of voices for ModelsLab
		 */
		static function get_list_modelslab_voices() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_modelslab_voices', array(
				// "///////////" => esc_html__( "///////////", "trx_addons" ),
				// "01rz4hbn" => esc_html__( "01RZ4HBN", "trx_addons" ),
				// "0bpzqnad" => esc_html__( "0BPZQNAD", "trx_addons" ),
				// "0di1b3np" => esc_html__( "0DI1B3NP", "trx_addons" ),
				// "1" => esc_html__( "Test1", "trx_addons" ),
				// "11-labs" => esc_html__( "11 labs", "trx_addons" ),
				// "2" => esc_html__( "2", "trx_addons" ),
				// "3" => esc_html__( "Test3", "trx_addons" ),
				// "123" => esc_html__( "123", "trx_addons" ),
				// "1536" => esc_html__( "test 1536", "trx_addons" ),
				// "1tsmserl" => esc_html__( "1TSMSERL", "trx_addons" ),
				// "2-fr-lp" => esc_html__( "test 2 FR LP", "trx_addons" ),
				// "2pac" => esc_html__( "2pac", "trx_addons" ),
				// "3q5157nw" => esc_html__( "3Q5157NW", "trx_addons" ),
				// "3unjhw7u" => esc_html__( "3UNJHW7U", "trx_addons" ),
				// "44kas8um" => esc_html__( "44KAS8UM", "trx_addons" ),
				// "4dc04de5-dad6-4add-b384-6" => esc_html__( "4dc04de5-dad6-4add-b384-639a68d710bf", "trx_addons" ),
				// "4zoot209" => esc_html__( "4ZOOT209", "trx_addons" ),
				// "5l5s21d0" => esc_html__( "5L5S21D0", "trx_addons" ),
				// "5usjdl83" => esc_html__( "5USJDL83", "trx_addons" ),
				// "72tqk8jm" => esc_html__( "72TQK8JM", "trx_addons" ),
				// "7a1vl9yg" => esc_html__( "7A1VL9YG", "trx_addons" ),
				// "7iv822bo" => esc_html__( "7IV822BO", "trx_addons" ),
				// "7ll9b1vz" => esc_html__( "7LL9B1VZ", "trx_addons" ),
				// "7xatyj9p" => esc_html__( "7XATYJ9P", "trx_addons" ),
				// "8auykga4" => esc_html__( "8AUYKGA4", "trx_addons" ),
				// "91lp4zf2" => esc_html__( "91LP4ZF2", "trx_addons" ),
				// "94fba54e-5ed2-43b3-ac9a-3" => esc_html__( "94fba54e-5ed2-43b3-ac9a-3b9df4502370", "trx_addons" ),
				// "9bdwfrrd" => esc_html__( "9BDWFRRD", "trx_addons" ),
				// "9kwqdvhn" => esc_html__( "9KWQDVHN", "trx_addons" ),
				// "9t77rzuk" => esc_html__( "9T77RZUK", "trx_addons" ),
				// "9ui" => esc_html__( "9ui", "trx_addons" ),
				// "a-sutil-arte" => esc_html__( "a sutil arte", "trx_addons" ),
				// "a3e0l5cw" => esc_html__( "A3E0L5CW", "trx_addons" ),
				// "aaarhovena1ca76f71-a7ad-4" => esc_html__( "aaarhovena1ca76f71-a7ad-4325-9fb0-50a5af457c9b", "trx_addons" ),
				// "aavvcc1713248587124" => esc_html__( "aavvcc_1713248587124", "trx_addons" ),
				// "aavvcc1713248592685" => esc_html__( "aavvcc_1713248592685", "trx_addons" ),
				// "ab" => esc_html__( "AB", "trx_addons" ),
				// "abascal" => esc_html__( "Abascal", "trx_addons" ),
				// "abc-20240430112600-34228" => esc_html__( "abc-20240430112600-34228", "trx_addons" ),
				// "abc-20240430112603-48604" => esc_html__( "abc-20240430112603-48604", "trx_addons" ),
				// "abc-20240430112608-15337" => esc_html__( "abc-20240430112608-15337", "trx_addons" ),
				// "abc-20240430112920-26805" => esc_html__( "abc-20240430112920-26805", "trx_addons" ),
				// "abc-20240430113023-11267" => esc_html__( "abc-20240430113023-11267", "trx_addons" ),
				// "abc-20240501124941-17733" => esc_html__( "abc-20240501124941-17733", "trx_addons" ),
				// "abc-20240501125534-49456" => esc_html__( "abc-20240501125534-49456", "trx_addons" ),
				// "abc-20240501125627-56872" => esc_html__( "abc-20240501125627-56872", "trx_addons" ),
				// "abc-20240501125927-48903" => esc_html__( "abc-20240501125927-48903", "trx_addons" ),
				// "abcdefg1713257363354" => esc_html__( "abcdefg_1713257363354", "trx_addons" ),
				// "abhaya" => esc_html__( "Abhaya", "trx_addons" ),
				// "ables-voice" => esc_html__( "Ables Voice", "trx_addons" ),
				// "adam" => esc_html__( "ADAM", "trx_addons" ),
				// "adam2" => esc_html__( "Adam2", "trx_addons" ),
				// "adam_01" => esc_html__( "Adam_01", "trx_addons" ),
				// "adamf1b15acf-d8fa-4e76-b3" => esc_html__( "Adamf1b15acf-d8fa-4e76-b315-b2fac990e736", "trx_addons" ),
				// "adauto121ef3a0-f992-4453-" => esc_html__( "ADAUTO121ef3a0-f992-4453-b974-5b77f093b6e8", "trx_addons" ),
				// "aditya" => esc_html__( "aditya", "trx_addons" ),
				// "admiralwilliam17132544341" => esc_html__( "admiralwilliam_1713254434115", "trx_addons" ),
				// "aga" => esc_html__( "AGA", "trx_addons" ),
				// "agente-smith44c076d1-2e13" => esc_html__( "Agente Smith44c076d1-2e13-4dd6-b4ea-bd6e6cbfad5c", "trx_addons" ),
				// "ahh" => esc_html__( "Ahh", "trx_addons" ),
				// "ahmet-eren-kolaylk-ben" => esc_html__( "Ahmet Eren Kolaylık (Ben)", "trx_addons" ),
				// "ai-eric-cartman" => esc_html__( "AI Eric Cartman", "trx_addons" ),
				// "ai-eric-cartman-artingio" => esc_html__( "AI Eric Cartman arting.io", "trx_addons" ),
				// "ai-michael-jackson" => esc_html__( "Ai Michael Jackson", "trx_addons" ),
				// "ai-michael-jackson-arting" => esc_html__( "Ai Michael Jackson arting.io", "trx_addons" ),
				// "aivoice137hiroyuki2" => esc_html__( "aivoice137hiroyuki2", "trx_addons" ),
				// "akaza" => esc_html__( "Akaza", "trx_addons" ),
				// "akito-shinonome" => esc_html__( "Akito Shinonome", "trx_addons" ),
				// "al-bertico" => esc_html__( "Al Bertico", "trx_addons" ),
				// "al-pacino" => esc_html__( "Al Pacino", "trx_addons" ),
				// "alania-female" => esc_html__( "Alania female", "trx_addons" ),
				// "alastor-artingio" => esc_html__( "Alastor arting.io", "trx_addons" ),
				// "alc9c6a666-dcfd-41bd-8f4c" => esc_html__( "ALc9c6a666-dcfd-41bd-8f4c-18df3219b231", "trx_addons" ),
				// "alessandra-buildbot" => esc_html__( "Alessandra-BuildBot", "trx_addons" ),
				// "alex" => esc_html__( "Alex", "trx_addons" ),
				// "alexander-lukashenko" => esc_html__( "Alexander Lukashenko", "trx_addons" ),
				// "alexandre-nogueira3889588" => esc_html__( "Alexandre Nogueira38895880-0eeb-4ec7-b17e-47e55f81d333", "trx_addons" ),
				// "alicia" => esc_html__( "Alicia", "trx_addons" ),
				// "alp" => esc_html__( "alp", "trx_addons" ),
				// "alysson" => esc_html__( "Alysson", "trx_addons" ),
				// "alysson18b41544-ea82-43bc" => esc_html__( "Alysson18b41544-ea82-43bc-a58a-09497e8729e2", "trx_addons" ),
				// "alysson2fe07887-9840-43d3" => esc_html__( "Alysson2fe07887-9840-43d3-bae1-94d6eef5d027", "trx_addons" ),
				// "alysson308c8be9-2d84-45d4" => esc_html__( "Alysson308c8be9-2d84-45d4-8ab4-a6a9822471e8", "trx_addons" ),
				// "aman90" => esc_html__( "Aman90", "trx_addons" ),
				// "american-1" => esc_html__( "american 1", "trx_addons" ),
				// "amy" => esc_html__( "Amy", "trx_addons" ),
				// "amélia-parrot" => esc_html__( "Amélia Parrot", "trx_addons" ),
				// "ana-maria-bragaba8588c9-6" => esc_html__( "ana maria bragaba8588c9-6e2e-41a2-a40d-0c35b726efa3", "trx_addons" ),
				// "anachildrenvoice" => esc_html__( "Ana_childrenvoice", "trx_addons" ),
				// "anderson-knightkley6b59ee" => esc_html__( "Anderson Knightkley6b59eec4-d58a-4d71-a13a-42a47cd12a59", "trx_addons" ),
				// "anderson-knightleyaf18168" => esc_html__( "Anderson Knightleyaf181688-d892-4435-a8ed-3b283f4e00a4", "trx_addons" ),
				// "andras" => esc_html__( "ANDRAS", "trx_addons" ),
				// "andre" => esc_html__( "ANDRE", "trx_addons" ),
				// "andreas" => esc_html__( "Andreas", "trx_addons" ),
				// "andrew" => esc_html__( "andrew", "trx_addons" ),
				// "andy" => esc_html__( "Andy", "trx_addons" ),
				// "anna" => esc_html__( "Anna", "trx_addons" ),
				// "anna2" => esc_html__( "Anna2", "trx_addons" ),
				// "annakidvoice" => esc_html__( "Anna_kidvoice", "trx_addons" ),
				// "antnio-marcos5c506d9e-33c" => esc_html__( "Antônio Marcos5c506d9e-33cd-404e-a4cc-78267b9446e8", "trx_addons" ),
				// "anubhav" => esc_html__( "anubhav", "trx_addons" ),
				// "anushka" => esc_html__( "Anushka", "trx_addons" ),
				// "arabica" => esc_html__( "arabica", "trx_addons" ),
				// "araboc-teacher-ict" => esc_html__( "araboc-teacher-ict", "trx_addons" ),
				// "arena4d6cf687-cc0d-43da-a" => esc_html__( "Arena4d6cf687-cc0d-43da-a428-615817e2c0f3", "trx_addons" ),
				// "ariana-grande" => esc_html__( "Ariana Grande", "trx_addons" ),
				// "armandoc894203b-e623-41c0" => esc_html__( "Armandoc894203b-e623-41c0-9dbd-4109668660e4", "trx_addons" ),
				// "arriane" => esc_html__( "arriane", "trx_addons" ),
				// "asdasdasdasdasdasd" => esc_html__( "asdasdasdasdasdasd", "trx_addons" ),
				// "ashley" => esc_html__( "Ashley", "trx_addons" ),
				// "asja" => esc_html__( "asja", "trx_addons" ),
				// "asja-arboc" => esc_html__( "asja arboc", "trx_addons" ),
				// "asmr-woman-artingio" => esc_html__( "ASMR Woman arting.io", "trx_addons" ),
				// "aua40mvg" => esc_html__( "AUA40MVG", "trx_addons" ),
				// "autida-1-lenrenzo" => esc_html__( "Autida test 1 Lenrenzo", "trx_addons" ),
				// "avb1713152514620" => esc_html__( "avb_1713152514620", "trx_addons" ),
				// "avi" => esc_html__( "Avi", "trx_addons" ),
				// "ayz" => esc_html__( "ayz", "trx_addons" ),
				// "az" => esc_html__( "az", "trx_addons" ),
				// "azzeddine" => esc_html__( "azzeddine", "trx_addons" ),
				// "b81lkh8r" => esc_html__( "B81LKH8R", "trx_addons" ),
				// "b981ec2d-6dd3-4b86-b64a-0" => esc_html__( "b981ec2d-6dd3-4b86-b64a-050533636848", "trx_addons" ),
				// "baba" => esc_html__( "baba", "trx_addons" ),
				// "babaarz" => esc_html__( "babaarz", "trx_addons" ),
				// "babrazam" => esc_html__( "babr_azam", "trx_addons" ),
				// "baka" => esc_html__( "Baka", "trx_addons" ),
				// "bala-caramelizada5b458d4b" => esc_html__( "Bala caramelizada5b458d4b-6401-444a-b8e2-cf4a7fc1a193", "trx_addons" ),
				// "barack-obama-artingio" => esc_html__( "Barack Obama arting.io", "trx_addons" ),
				// "beena" => esc_html__( "Beena", "trx_addons" ),
				// "beyonce" => esc_html__( "Beyonce", "trx_addons" ),
				// "biden" => esc_html__( "biden", "trx_addons" ),
				// "big-girl" => esc_html__( "Big Girl", "trx_addons" ),
				// "bigchunchun1713263175852" => esc_html__( "bigchunchun_1713263175852", "trx_addons" ),
				// "bigt" => esc_html__( "BigT", "trx_addons" ),
				// "bill" => esc_html__( "bill", "trx_addons" ),
				// "bill-anderson" => esc_html__( "Bill Anderson", "trx_addons" ),
				// "bill1713337768433" => esc_html__( "bill_1713337768433", "trx_addons" ),
				// "billgates1713332678742" => esc_html__( "billgates_1713332678742", "trx_addons" ),
				// "billgatesnew1713334315616" => esc_html__( "billgatesnew_1713334315616", "trx_addons" ),
				// "billie" => esc_html__( "Billie", "trx_addons" ),
				// "billieamour" => esc_html__( "Billie_Amour", "trx_addons" ),
				// "billyalish" => esc_html__( "billyalish", "trx_addons" ),
				// "blackgoku" => esc_html__( "blackgoku", "trx_addons" ),
				// "borgesdd8b8414-f8b9-4516-" => esc_html__( "Borgesdd8b8414-f8b9-4516-826e-4d05fc75401b", "trx_addons" ),
				// "braina86f3e3b-247c-4ddb-b" => esc_html__( "Braina86f3e3b-247c-4ddb-b81c-0f27e968be64", "trx_addons" ),
				// "brian" => esc_html__( "Brian", "trx_addons" ),
				// "brian-v0" => esc_html__( "Brian v0", "trx_addons" ),
				// "bridgette" => esc_html__( "Bridgette", "trx_addons" ),
				// "britney" => esc_html__( "britney", "trx_addons" ),
				// "bruce" => esc_html__( "Bruce", "trx_addons" ),
				// "bruce13054a72-eff2-4aa0-9" => esc_html__( "Bruce13054a72-eff2-4aa0-9f14-78acb093be17", "trx_addons" ),
				// "c1js3oii" => esc_html__( "C1JS3OII", "trx_addons" ),
				// "calliope-mori-holoen" => esc_html__( "Calliope Mori 💀 holoEN", "trx_addons" ),
				// "carlosb09d893d-7a4f-4a38-" => esc_html__( "CARLOSb09d893d-7a4f-4a38-a719-2a60683cd91a", "trx_addons" ),
				// "caro-smithca70ce2d-51ba-4" => esc_html__( "Ícaro Smithca70ce2d-51ba-4ecd-9214-c4013e1a4968", "trx_addons" ),
				// "carolina" => esc_html__( "Carolina", "trx_addons" ),
				// "carro-de-som4e786a46-96e2" => esc_html__( "CARRO DE SOM4e786a46-96e2-4845-b1c8-9b63fb590e74", "trx_addons" ),
				// "castro-5a9b15c7-64cb-4a0f" => esc_html__( "Castro 5a9b15c7-64cb-4a0f-91ab-474c30651f88", "trx_addons" ),
				// "ccaas1713154654068" => esc_html__( "ccaas_1713154654068", "trx_addons" ),
				// "ccaas1713154661353" => esc_html__( "ccaas_1713154661353", "trx_addons" ),
				// "ccaas1713154667824" => esc_html__( "ccaas_1713154667824", "trx_addons" ),
				// "ccaas1713154715852" => esc_html__( "ccaas_1713154715852", "trx_addons" ),
				// "ccaas1713160898707" => esc_html__( "ccaas_1713160898707", "trx_addons" ),
				// "ccaas1713161149218" => esc_html__( "ccaas_1713161149218", "trx_addons" ),
				// "ccaas1713161373626" => esc_html__( "ccaas_1713161373626", "trx_addons" ),
				// "ccaas1713161887468" => esc_html__( "ccaas_1713161887468", "trx_addons" ),
				// "ccaas1713161995362" => esc_html__( "ccaas_1713161995362", "trx_addons" ),
				// "ccaas1713163434159" => esc_html__( "ccaas_1713163434159", "trx_addons" ),
				// "ccaas1713163460774" => esc_html__( "ccaas_1713163460774", "trx_addons" ),
				// "ccaas1713163598558" => esc_html__( "ccaas_1713163598558", "trx_addons" ),
				// "ccaas1713163674726" => esc_html__( "ccaas_1713163674726", "trx_addons" ),
				// "ccda1713247953054" => esc_html__( "ccda_1713247953054", "trx_addons" ),
				// "cdi-mario" => esc_html__( "CDI Mario", "trx_addons" ),
				// "celeste-campbell" => esc_html__( "Celeste Campbell", "trx_addons" ),
				// "celestes-voice" => esc_html__( "Celeste's Voice", "trx_addons" ),
				// "chaerra-coreano74c26062-e" => esc_html__( "Chaerra. (coreano)74c26062-e03f-4148-bcf1-28bedd49adb0", "trx_addons" ),
				// "charli" => esc_html__( "Charli", "trx_addons" ),
				// "charlote" => esc_html__( "Charlote", "trx_addons" ),
				// "charlotea69b9997-70c9-444" => esc_html__( "Charlotea69b9997-70c9-4447-90a6-75841f488bf7", "trx_addons" ),
				// "cheroll" => esc_html__( "Cheroll", "trx_addons" ),
				// "chinese-0" => esc_html__( "Chinese-0", "trx_addons" ),
				// "chinese-1" => esc_html__( "Chinese-1", "trx_addons" ),
				// "chipi-chapa" => esc_html__( "chipi-chapa", "trx_addons" ),
				// "chris" => esc_html__( "Chris", "trx_addons" ),
				// "chris-smith" => esc_html__( "Chris Smith", "trx_addons" ),
				// "christopher-w" => esc_html__( "Christopher W", "trx_addons" ),
				// "cjp-v3" => esc_html__( "CJP v3", "trx_addons" ),
				// "clay" => esc_html__( "Clay", "trx_addons" ),
				// "clone-ee465e829-7623-44ea" => esc_html__( "Clone Testee465e829-7623-44ea-b4e3-897f6657317e", "trx_addons" ),
				// "clone-voice-123" => esc_html__( "Clone Voice 123", "trx_addons" ),
				// "cloned-voice" => esc_html__( "Cloned Voice", "trx_addons" ),
				// "cloning-voice" => esc_html__( "Cloning voice", "trx_addons" ),
				// "companionship" => esc_html__( "companionship", "trx_addons" ),
				// "country-young-male" => esc_html__( "Country - Young Male", "trx_addons" ),
				// "criativobbdace8c-eb0a-487" => esc_html__( "criativobbdace8c-eb0a-4875-b767-ab127ac51010", "trx_addons" ),
				// "crysta-fae" => esc_html__( "Crysta Fae", "trx_addons" ),
				// "cw-take-2" => esc_html__( "CW Take 2", "trx_addons" ),
				// "dan-hypno" => esc_html__( "Dan Hypno", "trx_addons" ),
				// "dani" => esc_html__( "Dani", "trx_addons" ),
				// "daniel-ramos" => esc_html__( "Daniel Ramos", "trx_addons" ),
				// "daniel84e94f87-b169-45d7-" => esc_html__( "Daniel84e94f87-b169-45d7-aea6-32a791fb2826", "trx_addons" ),
				// "daniel96b12620-ff59-4357-" => esc_html__( "Daniel96b12620-ff59-4357-a052-c5b6f9366da1", "trx_addons" ),
				// "danielefa4f05f-e846-4982-" => esc_html__( "Danielefa4f05f-e846-4982-ad12-428653b8a0fd", "trx_addons" ),
				// "danzel1713235778406" => esc_html__( "danzel_1713235778406", "trx_addons" ),
				// "dashauige1713261477760" => esc_html__( "dashauige_1713261477760", "trx_addons" ),
				// "david" => esc_html__( "David", "trx_addons" ),
				// "david-henrique" => esc_html__( "David Henrique", "trx_addons" ),
				// "david-xavier84677897-a2a3" => esc_html__( "David Xavier84677897-a2a3-4b00-828d-5fc006e18332", "trx_addons" ),
				// "dawd1713261101754" => esc_html__( "dawd_1713261101754", "trx_addons" ),
				// "dcy1713265734807" => esc_html__( "dcy_1713265734807", "trx_addons" ),
				// "dcy1713334266067" => esc_html__( "dcy_1713334266067", "trx_addons" ),
				// "dd" => esc_html__( "DD", "trx_addons" ),
				// "ddasd1713261300766" => esc_html__( "ddasd_1713261300766", "trx_addons" ),
				// "ddd1713249123024" => esc_html__( "ddd_1713249123024", "trx_addons" ),
				// "deep-turkish-web-english" => esc_html__( "Deep Turkish Web English", "trx_addons" ),
				// "demo" => esc_html__( "demo", "trx_addons" ),
				// "demo-1-voz" => esc_html__( "Demo 1 Voz", "trx_addons" ),
				// "demo46578" => esc_html__( "demo46578", "trx_addons" ),
				// "denzel" => esc_html__( "denzel", "trx_addons" ),
				// "denzel-washington" => esc_html__( "Denzel Washington", "trx_addons" ),
				// "dfggc1713263347466" => esc_html__( "dfggc_1713263347466", "trx_addons" ),
				// "dhiraj" => esc_html__( "Dhiraj", "trx_addons" ),
				// "diana-argentina3a3113a3-7" => esc_html__( "Diana (argentina)3a3113a3-7a1b-4b61-8b08-79d303de7383", "trx_addons" ),
				// "diana-espanholb4f53a6a-80" => esc_html__( "Diana (espanhol)b4f53a6a-80b2-490b-baab-db11c2ee72c8", "trx_addons" ),
				// "dilip-joshi" => esc_html__( "Dilip Joshi", "trx_addons" ),
				// "dio-brando" => esc_html__( "Dio Brando", "trx_addons" ),
				// "dio-brando-artingio" => esc_html__( "Dio Brando arting.io", "trx_addons" ),
				// "djt" => esc_html__( "DjT", "trx_addons" ),
				// "dkwda1713259455561" => esc_html__( "dkwda_1713259455561", "trx_addons" ),
				// "dkwda1713261227285" => esc_html__( "dkwda_1713261227285", "trx_addons" ),
				// "domingo" => esc_html__( "Domingo", "trx_addons" ),
				// "donald-duck" => esc_html__( "donald-duck", "trx_addons" ),
				// "donald-trump" => esc_html__( "Donald Trump", "trx_addons" ),
				// "donald-trump-artingio" => esc_html__( "Donald Trump arting.io", "trx_addons" ),
				// "dore-gpt" => esc_html__( "Dore GPT", "trx_addons" ),
				// "dpkbz04m" => esc_html__( "DPKBZ04M", "trx_addons" ),
				// "dr-joe" => esc_html__( "Dr. Joe", "trx_addons" ),
				// "dr-rob-white" => esc_html__( "Dr. Rob White", "trx_addons" ),
				// "drachenlord-artingio" => esc_html__( "Drachenlord arting.io", "trx_addons" ),
				// "drake" => esc_html__( "Drake", "trx_addons" ),
				// "drake-artingio" => esc_html__( "Drake ‍‍ arting.io", "trx_addons" ),
				// "drewsetis" => esc_html__( "drewsetis", "trx_addons" ),
				// "dudad8f25b61-f3c8-49b1-b4" => esc_html__( "Dudad8f25b61-f3c8-49b1-b495-e9679ed8d812", "trx_addons" ),
				// "duecas1713265066591" => esc_html__( "duecas_1713265066591", "trx_addons" ),
				// "duecas1713265395560" => esc_html__( "duecas_1713265395560", "trx_addons" ),
				// "duglas-buildbot" => esc_html__( "Duglas-BuildBot", "trx_addons" ),
				// "duymingmc" => esc_html__( "DuyMing_MC", "trx_addons" ),
				// "dw1pmmpl" => esc_html__( "DW1PMMPL", "trx_addons" ),
				// "e" => esc_html__( "teste", "trx_addons" ),
				// "e-1-295ec300df-a6af-4654-" => esc_html__( "teste 1 295ec300df-a6af-4654-9b43-a5ba91248024", "trx_addons" ),
				// "e-1a33095ec-5e99-4de4-bff" => esc_html__( "teste 1a33095ec-5e99-4de4-bff0-3f84dd0c0fe4", "trx_addons" ),
				// "e-7d64d4ed-22fc-4c8a-9d49" => esc_html__( "teste 7d64d4ed-22fc-4c8a-9d49-fee963498965", "trx_addons" ),
				// "e-jacile" => esc_html__( "TESTE-JACILE", "trx_addons" ),
				// "e-jaimebbc76fdd-ffd6-49cb" => esc_html__( "Teste Jaimebbc76fdd-ffd6-49cb-a29e-6239e71fa72f", "trx_addons" ),
				// "e-ruim-2af6c765a-baa1-454" => esc_html__( "Teste ruim 2af6c765a-baa1-4547-b112-277122b2f07f", "trx_addons" ),
				// "e-ruimadfb0654-c9e2-4e2c-" => esc_html__( "Teste ruimadfb0654-c9e2-4e2c-bb6a-7a343dfa29ec", "trx_addons" ),
				// "e010b5a0c1f-3b49-4da4-bb7" => esc_html__( "TESTE_010b5a0c1f-3b49-4da4-bb71-7f380ddceee1", "trx_addons" ),
				// "e1" => esc_html__( "teste1", "trx_addons" ),
				// "e160fd28a-600c-4c98-a3db-" => esc_html__( "teste160fd28a-600c-4c98-a3db-9237004ffdd2", "trx_addons" ),
				// "e33d3d22-b3e0-44f1-ac71-6" => esc_html__( "e33d3d22-b3e0-44f1-ac71-64bb28af0551", "trx_addons" ),
				// "e66228d58-eadc-4a80-a91c-" => esc_html__( "teste66228d58-eadc-4a80-a91c-c2d60981bfca", "trx_addons" ),
				// "e7thua48" => esc_html__( "E7THUA48", "trx_addons" ),
				// "ea1a78551-4eef-44dd-acff-" => esc_html__( "Testea1a78551-4eef-44dd-acff-70d247930682", "trx_addons" ),
				// "echo" => esc_html__( "Echo", "trx_addons" ),
				// "ed-sheeran" => esc_html__( "Ed Sheeran", "trx_addons" ),
				// "edson-buildbot" => esc_html__( "Edson-BuildBot", "trx_addons" ),
				// "eeddd1713255467273" => esc_html__( "testeeddd_1713255467273", "trx_addons" ),
				// "efvfp0jf" => esc_html__( "EFVFP0JF", "trx_addons" ),
				// "eileen-101" => esc_html__( "Eileen 101", "trx_addons" ),
				// "elan" => esc_html__( "elan", "trx_addons" ),
				// "elianabb72e800-5193-4938-" => esc_html__( "Elianabb72e800-5193-4938-aa3b-d8e9e4815032", "trx_addons" ),
				// "elon" => esc_html__( "Elon", "trx_addons" ),
				// "elon-musk" => esc_html__( "Elon Musk", "trx_addons" ),
				// "elon-musk-artingio" => esc_html__( "Elon Musk arting.io", "trx_addons" ),
				// "elon_mask" => esc_html__( "Elon mask", "trx_addons" ),
				// "elonmusk" => esc_html__( "ElonMusk", "trx_addons" ),
				// "elrpq9ss" => esc_html__( "ELRPQ9SS", "trx_addons" ),
				// "elvis" => esc_html__( "elvis", "trx_addons" ),
				// "elvis2" => esc_html__( "Elvis2", "trx_addons" ),
				// "elvvoice" => esc_html__( "elv_voice", "trx_addons" ),
				// "emi" => esc_html__( "emi", "trx_addons" ),
				// "eminem" => esc_html__( "Eminem", "trx_addons" ),
				// "eng" => esc_html__( "Eng", "trx_addons" ),
				// "enrique" => esc_html__( "Enrique", "trx_addons" ),
				// "enrique-rocha" => esc_html__( "Enrique rocha", "trx_addons" ),
				// "ephey" => esc_html__( "Ephey", "trx_addons" ),
				// "ericb" => esc_html__( "eric_b", "trx_addons" ),
				// "et6nxwcp" => esc_html__( "ET6NXWCP", "trx_addons" ),
				// "eu2526a6444-861d-422a-93f" => esc_html__( "eu2526a6444-861d-422a-93f0-bb200ce66599", "trx_addons" ),
				// "everton" => esc_html__( "Everton", "trx_addons" ),
				// "evertona4ab3595-8404-4043" => esc_html__( "Evertona4ab3595-8404-4043-ab03-64db582289b8", "trx_addons" ),
				// "everybodys-dad" => esc_html__( "Everybody's Dad", "trx_addons" ),
				// "evvoice2" => esc_html__( "ev_voice2", "trx_addons" ),
				// "example" => esc_html__( "example", "trx_addons" ),
				// "f68d82k0" => esc_html__( "F68D82K0", "trx_addons" ),
				// "fbrqlpo4" => esc_html__( "FBRQLPO4", "trx_addons" ),
				// "felipeaffc85fe-ca38-4ed5-" => esc_html__( "Felipeaffc85fe-ca38-4ed5-af04-0ff901b6c9b4", "trx_addons" ),
				// "felipedc6caa75-9105-4d6b-" => esc_html__( "Felipedc6caa75-9105-4d6b-8e71-a0c8d14c37c0", "trx_addons" ),
				// "felipeebe1c217-6f25-496f-" => esc_html__( "Felipeebe1c217-6f25-496f-807c-7c60372d88ca", "trx_addons" ),
				// "female1" => esc_html__( "female1", "trx_addons" ),
				// "ffg" => esc_html__( "Ffg", "trx_addons" ),
				// "fgg" => esc_html__( "Fgg", "trx_addons" ),
				// "fh" => esc_html__( "FH", "trx_addons" ),
				// "fh-reevs" => esc_html__( "fh reevs", "trx_addons" ),
				// "fred" => esc_html__( "Fred", "trx_addons" ),
				// "fuckxxx1713248308944" => esc_html__( "fuckxxx_1713248308944", "trx_addons" ),
				// "fuckxxx1713248545913" => esc_html__( "fuckxxx_1713248545913", "trx_addons" ),
				// "fuckxxx1713248568116" => esc_html__( "fuckxxx_1713248568116", "trx_addons" ),
				// "fwfwf1713261369943" => esc_html__( "fwfwf_1713261369943", "trx_addons" ),
				// "gates21713333626710" => esc_html__( "gates2_1713333626710", "trx_addons" ),
				// "gates31713333746213" => esc_html__( "gates3_1713333746213", "trx_addons" ),
				// "gdadsadg1713257399261" => esc_html__( "gdadsadg_1713257399261", "trx_addons" ),
				// "ggb1713188573980" => esc_html__( "ggb_1713188573980", "trx_addons" ),
				// "ggb1713246887911" => esc_html__( "ggb_1713246887911", "trx_addons" ),
				// "ggb1713247792361" => esc_html__( "ggb_1713247792361", "trx_addons" ),
				// "ggea" => esc_html__( "ggea", "trx_addons" ),
				// "ggefayhm" => esc_html__( "GGEFAYHM", "trx_addons" ),
				// "ggese1713173576761" => esc_html__( "ggese_1713173576761", "trx_addons" ),
				// "gggggqqqqq1713257221685" => esc_html__( "gggggqqqqq_1713257221685", "trx_addons" ),
				// "ghostface-scary-movie" => esc_html__( "Ghostface scary movie", "trx_addons" ),
				// "ghostface-scary-movie-art" => esc_html__( "Ghostface scary movie arting.io", "trx_addons" ),
				// "ghostface-tiktok-tts" => esc_html__( "Ghostface TikTok TTS", "trx_addons" ),
				// "ghostface-tiktok-tts-arti" => esc_html__( "Ghostface TikTok TTS arting.io", "trx_addons" ),
				// "ghostface-v2" => esc_html__( "Ghostface V2", "trx_addons" ),
				// "ghostface-v2-artingio" => esc_html__( "Ghostface V2 arting.io", "trx_addons" ),
				// "ghostfacekillah" => esc_html__( "Ghostfacekillah", "trx_addons" ),
				// "gianni-1" => esc_html__( "Gianni 1", "trx_addons" ),
				// "girll" => esc_html__( "Girll", "trx_addons" ),
				// "gl1" => esc_html__( "GL1", "trx_addons" ),
				// "glamrock-freddy1" => esc_html__( "Glamrock Freddy1", "trx_addons" ),
				// "glamrock-freddy1-artingio" => esc_html__( "Glamrock Freddy1 arting.io", "trx_addons" ),
				// "glinda4e2fa2ec-0c27-424f-" => esc_html__( "Glinda4e2fa2ec-0c27-424f-8be5-7e54226b4a07", "trx_addons" ),
				// "glptbr" => esc_html__( "GLptBR", "trx_addons" ),
				// "gojo-artingio" => esc_html__( "Gojo arting.io", "trx_addons" ),
				// "goku" => esc_html__( "Goku", "trx_addons" ),
				// "goku-artingio" => esc_html__( "Goku arting.io", "trx_addons" ),
				// "gokuaf3e33c4-4a9b-4598-a8" => esc_html__( "GOKUaf3e33c4-4a9b-4598-a8ed-ec5889bf2300", "trx_addons" ),
				// "golki" => esc_html__( "Golki", "trx_addons" ),
				// "good" => esc_html__( "Good", "trx_addons" ),
				// "gracedbdc4434-92c2-49f9-a" => esc_html__( "Gracedbdc4434-92c2-49f9-a6c9-a368af724db7", "trx_addons" ),
				// "gregory-allen" => esc_html__( "Gregory Allen", "trx_addons" ),
				// "grok" => esc_html__( "groktest", "trx_addons" ),
				// "gtirtiti" => esc_html__( "gtirtiti", "trx_addons" ),
				// "guayo" => esc_html__( "Guayo", "trx_addons" ),
				// "guitar-2" => esc_html__( "guitar 2", "trx_addons" ),
				// "gunna" => esc_html__( "gunna", "trx_addons" ),
				// "h" => esc_html__( "h", "trx_addons" ),
				// "h9gf3dmo" => esc_html__( "H9GF3DMO", "trx_addons" ),
				// "habib" => esc_html__( "Habib", "trx_addons" ),
				// "hady-jaya-indonesia" => esc_html__( "Hady Jaya (Indonesia)", "trx_addons" ),
				// "hagqb5wn" => esc_html__( "HAGQB5WN", "trx_addons" ),
				// "haharr" => esc_html__( "haharr", "trx_addons" ),
				// "hans" => esc_html__( "Hans", "trx_addons" ),
				// "harper" => esc_html__( "Harper", "trx_addons" ),
				// "harper5e503b54-9b46-45bf-" => esc_html__( "Harper5e503b54-9b46-45bf-8fdd-4467dafbdbcf", "trx_addons" ),
				// "harrison" => esc_html__( "Harrison", "trx_addons" ),
				// "hatsune-miku-model" => esc_html__( "Hatsune Miku Model", "trx_addons" ),
				// "hatsune-miku-model-arting" => esc_html__( "Hatsune Miku Model arting.io", "trx_addons" ),
				// "heik3yvz" => esc_html__( "HEIK3YVZ", "trx_addons" ),
				// "hello" => esc_html__( "Hello", "trx_addons" ),
				// "hello1" => esc_html__( "Hello1", "trx_addons" ),
				// "hellyeah" => esc_html__( "Hellyeah", "trx_addons" ),
				// "hetfield" => esc_html__( "hetfield", "trx_addons" ),
				// "hh" => esc_html__( "hh", "trx_addons" ),
				// "hhf1713264290041" => esc_html__( "hhf_1713264290041", "trx_addons" ),
				// "hhhh1713249475356" => esc_html__( "hhhh_1713249475356", "trx_addons" ),
				// "hhqo7p95" => esc_html__( "HHQO7P95", "trx_addons" ),
				// "hip" => esc_html__( "HIP", "trx_addons" ),
				// "hjknyytb" => esc_html__( "HJKNYYTB", "trx_addons" ),
				// "hmk" => esc_html__( "Hmk", "trx_addons" ),
				// "homer-simpson" => esc_html__( "Homer Simpson", "trx_addons" ),
				// "homer-simpson-artingio" => esc_html__( "Homer Simpson arting.io", "trx_addons" ),
				// "hu-tao" => esc_html__( "Hu Tao", "trx_addons" ),
				// "hu-tao-artingio" => esc_html__( "Hu Tao arting.io", "trx_addons" ),
				// "hubert" => esc_html__( "Hubert", "trx_addons" ),
				// "hvijozes" => esc_html__( "HVIJOZES", "trx_addons" ),
				// "ice-spice-v5" => esc_html__( "Ice Spice V5", "trx_addons" ),
				// "ich-1-deutsch" => esc_html__( "ich 1 deutsch", "trx_addons" ),
				// "ichimaru" => esc_html__( "ichimaru", "trx_addons" ),
				// "iigo" => esc_html__( "Iñigo", "trx_addons" ),
				// "iisdad1713257969289" => esc_html__( "iisdad_1713257969289", "trx_addons" ),
				// "ij7771713265418689" => esc_html__( "ij777_1713265418689", "trx_addons" ),
				// "indian-hhhhhhhhhh" => esc_html__( "Indian hhhhhhhhhh", "trx_addons" ),
				// "indo" => esc_html__( "indo", "trx_addons" ),
				// "ip78f0cf45-81be-4b55-9b8e" => esc_html__( "IP78f0cf45-81be-4b55-9b8e-414db9cd1c07", "trx_addons" ),
				// "ironi313e4a21-b92c-46dd-b" => esc_html__( "Ironi313e4a21-b92c-46dd-bb89-57a6093330d5", "trx_addons" ),
				// "isabela8bd19001-ec64-414b" => esc_html__( "Isabela8bd19001-ec64-414b-815c-168f8204f6f2", "trx_addons" ),
				// "isac-20b26d1719-17e8-45d9" => esc_html__( "Isac 2.0b26d1719-17e8-45d9-9b8b-bd999683e5f9", "trx_addons" ),
				// "isacd028bfcc-397d-49a8-b9" => esc_html__( "Isacd028bfcc-397d-49a8-b9e8-9065cdfce0a1", "trx_addons" ),
				// "ishowspeed" => esc_html__( "IShowSpeed", "trx_addons" ),
				// "ishowspeed-artingio" => esc_html__( "IShowSpeed arting.io", "trx_addons" ),
				// "j7rc8zw3" => esc_html__( "J7RC8ZW3", "trx_addons" ),
				// "jacile-buildbot" => esc_html__( "Jacile-BuildBot", "trx_addons" ),
				// "jack_reach" => esc_html__( "jack_reach", "trx_addons" ),
				// "jackie-chan" => esc_html__( "Jackie Chan", "trx_addons" ),
				// "jackson" => esc_html__( "jackson", "trx_addons" ),
				// "jacob-3" => esc_html__( "Jacob 3", "trx_addons" ),
				// "jacob-4" => esc_html__( "Jacob 4", "trx_addons" ),
				// "jacob-5" => esc_html__( "Jacob 5", "trx_addons" ),
				// "jacob0326" => esc_html__( "Jacob0326", "trx_addons" ),
				// "jacob0802" => esc_html__( "Jacob0802", "trx_addons" ),
				// "jacob2" => esc_html__( "Jacob2", "trx_addons" ),
				// "jacob3" => esc_html__( "Jacob3", "trx_addons" ),
				// "jacober" => esc_html__( "jacober", "trx_addons" ),
				// "jacquin8d3458e9-918a-4cde" => esc_html__( "jacquin8d3458e9-918a-4cde-856d-f920e719cd83", "trx_addons" ),
				// "jaimie" => esc_html__( "Jaimie", "trx_addons" ),
				// "james" => esc_html__( "JAMES", "trx_addons" ),
				// "james-bivins" => esc_html__( "James Bivins", "trx_addons" ),
				// "jameshs1" => esc_html__( "Jameshs1", "trx_addons" ),
				// "jamila24" => esc_html__( "jamila24", "trx_addons" ),
				// "jansim" => esc_html__( "jansim", "trx_addons" ),
				// "jaobe-lacerda2547aaf7-f03" => esc_html__( "Jaobe Lacerda2547aaf7-f035-4e2e-b5b8-409378ed59c7", "trx_addons" ),
				// "jared-leto" => esc_html__( "Jared Leto", "trx_addons" ),
				// "jason-griffith-sonic" => esc_html__( "Jason Griffith Sonic", "trx_addons" ),
				// "jason-griffith-sonic-arti" => esc_html__( "Jason Griffith Sonic arting.io", "trx_addons" ),
				// "jasonw" => esc_html__( "JasonW", "trx_addons" ),
				// "jasperf1dc06a5-2809-44e7-" => esc_html__( "Jasperf1dc06a5-2809-44e7-9007-560e9f8e9821", "trx_addons" ),
				// "javed-bashir" => esc_html__( "Javed Bashir", "trx_addons" ),
				// "javi" => esc_html__( "javi", "trx_addons" ),
				// "javier1" => esc_html__( "javier1", "trx_addons" ),
				// "javier12" => esc_html__( "javier12", "trx_addons" ),
				// "jay-z" => esc_html__( "Jay Z", "trx_addons" ),
				// "jdeep" => esc_html__( "jdeep", "trx_addons" ),
				// "jeremy" => esc_html__( "Jeremy", "trx_addons" ),
				// "jessica" => esc_html__( "Jessica", "trx_addons" ),
				// "jij4a0w2" => esc_html__( "JIJ4A0W2", "trx_addons" ),
				// "jim-carrey" => esc_html__( "Jim Carrey", "trx_addons" ),
				// "jjwoman" => esc_html__( "JJ_woman", "trx_addons" ),
				// "jo-hora4cd9333f-3290-4eae" => esc_html__( "JO - HORA4cd9333f-3290-4eae-ab10-d3f43deed4bc", "trx_addons" ),
				// "joabe" => esc_html__( "joabe", "trx_addons" ),
				// "joabe-2" => esc_html__( "Joabe 2", "trx_addons" ),
				// "joabe-lacerda" => esc_html__( "Joabe Lacerda", "trx_addons" ),
				// "joabelacerda" => esc_html__( "joabe_lacerda", "trx_addons" ),
				// "joabelacerda2" => esc_html__( "joabe_lacerda2", "trx_addons" ),
				// "joe-biden" => esc_html__( "Joe Biden", "trx_addons" ),
				// "joe-biden-for-my-assignme" => esc_html__( "Joe Biden for my assignment (for you)", "trx_addons" ),
				// "joe-rogan-artingio" => esc_html__( "Joe Rogan arting.io", "trx_addons" ),
				// "joelson-882f10e2-de7e-421" => esc_html__( "JOELSON 882f10e2-de7e-421d-8a98-4e7a179ac037", "trx_addons" ),
				// "john" => esc_html__( "John", "trx_addons" ),
				// "johnny-depp" => esc_html__( "Johnny Depp", "trx_addons" ),
				// "jokowi" => esc_html__( "Jokowi", "trx_addons" ),
				// "jokowi-artingio" => esc_html__( "Jokowi arting.io", "trx_addons" ),
				// "jooh" => esc_html__( "jooh", "trx_addons" ),
				// "joshua-graham" => esc_html__( "Joshua Graham", "trx_addons" ),
				// "joshua-graham-artingio" => esc_html__( "Joshua Graham arting.io", "trx_addons" ),
				// "jota-jesus738dd0df-b97d-4" => esc_html__( "JOTA JESUS738dd0df-b97d-4c69-a729-01dcd376c1dd", "trx_addons" ),
				// "jota-rico44a4fdb1-7afb-4f" => esc_html__( "JOTA - RICO44a4fdb1-7afb-4fa3-92fe-4e381ad8a401", "trx_addons" ),
				// "jotaro-kujo" => esc_html__( "Jotaro Kujo", "trx_addons" ),
				// "jotaro-kujo-artingio" => esc_html__( "Jotaro Kujo arting.io", "trx_addons" ),
				// "jr-voice" => esc_html__( "JR voice", "trx_addons" ),
				// "jschlatt" => esc_html__( "Jschlatt", "trx_addons" ),
				// "jschlatt-artingio" => esc_html__( "Jschlatt arting.io", "trx_addons" ),
				// "ju" => esc_html__( "ju", "trx_addons" ),
				// "ju68fdf053-e3a3-4c7b-9ff2" => esc_html__( "Ju68fdf053-e3a3-4c7b-9ff2-4a330fb96c10", "trx_addons" ),
				// "juan" => esc_html__( "juan", "trx_addons" ),
				// "juice-wrld-best-model-out" => esc_html__( "juice wrld best model out", "trx_addons" ),
				// "juice-wrld-jw3-sessions" => esc_html__( "Juice WRLD (JW3 Sessions)", "trx_addons" ),
				// "juice-wrld-la-voice-2019" => esc_html__( "Juice WRLD (Latest Voice 2019)", "trx_addons" ),
				// "juice-wrld-mellow-voice-a" => esc_html__( "Juice WRLD (Mellow Voice) arting.io", "trx_addons" ),
				// "juice-wrld-new-rock-voice" => esc_html__( "Juice WRLD (New Rock Voice)", "trx_addons" ),
				// "juice-wrld-raspy-voice" => esc_html__( "Juice WRLD (Raspy Voice)", "trx_addons" ),
				// "juice-wrld-studio-model" => esc_html__( "Juice WRLD (Studio Model)", "trx_addons" ),
				// "juice-wrld-v2-artingio" => esc_html__( "Juice wrld V2 arting.io", "trx_addons" ),
				// "juice-wrld-v4" => esc_html__( "Juice WRLD v4", "trx_addons" ),
				// "juice-wrld-v4-artingio" => esc_html__( "Juice WRLD v4 arting.io", "trx_addons" ),
				// "juliana69f1376f-c406-4194" => esc_html__( "Juliana69f1376f-c406-4194-a680-4829cfb3a48d", "trx_addons" ),
				// "juliano-buildbot" => esc_html__( "Juliano-BuildBot", "trx_addons" ),
				// "jungkook" => esc_html__( "jungkook", "trx_addons" ),
				// "junior-nunes9112c9a6-20ea" => esc_html__( "Junior Nunes9112c9a6-20ea-4e40-83a1-81b63e1c416b", "trx_addons" ),
				// "justin" => esc_html__( "justin", "trx_addons" ),
				// "justin-bieber" => esc_html__( "Justin Bieber", "trx_addons" ),
				// "k5xldctq" => esc_html__( "K5XLDCTQ", "trx_addons" ),
				// "kanye" => esc_html__( "Kanye", "trx_addons" ),
				// "kanye-west" => esc_html__( "Kanye West", "trx_addons" ),
				// "kanye-west-best-quality-a" => esc_html__( "Kanye West (Best Quality) arting.io", "trx_addons" ),
				// "kanye-west-new" => esc_html__( "Kanye West (New)", "trx_addons" ),
				// "karen" => esc_html__( "Karen", "trx_addons" ),
				// "kasinsky21621ef8-9d7b-437" => esc_html__( "kasinsky21621ef8-9d7b-4379-807d-0840388c9ba4", "trx_addons" ),
				// "katdvuu9" => esc_html__( "KATDVUU9", "trx_addons" ),
				// "kefa" => esc_html__( "kefa", "trx_addons" ),
				// "kg" => esc_html__( "KG", "trx_addons" ),
				// "khabib" => esc_html__( "Khabib", "trx_addons" ),
				// "kili" => esc_html__( "Kili", "trx_addons" ),
				// "kim" => esc_html__( "kim", "trx_addons" ),
				// "kim-kardashian" => esc_html__( "Kim Kardashian", "trx_addons" ),
				// "king" => esc_html__( "King", "trx_addons" ),
				// "king-julien" => esc_html__( "King Julien", "trx_addons" ),
				// "king-julien-artingio" => esc_html__( "King Julien arting.io", "trx_addons" ),
				// "kingjack" => esc_html__( "KingJack", "trx_addons" ),
				// "kiona" => esc_html__( "Kiona", "trx_addons" ),
				// "kishlaya" => esc_html__( "Kishlaya", "trx_addons" ),
				// "kkk" => esc_html__( "Kkk", "trx_addons" ),
				// "klaus" => esc_html__( "Klaus", "trx_addons" ),
				// "knock" => esc_html__( "knock", "trx_addons" ),
				// "kristin-mccranie" => esc_html__( "Kristin McCranie", "trx_addons" ),
				// "kurirai-original-voice" => esc_html__( "Kurirai Original Voice", "trx_addons" ),
				// "kuriraiboss" => esc_html__( "KuriraiBoss", "trx_addons" ),
				// "ky9y04ot" => esc_html__( "KY9Y04OT", "trx_addons" ),
				// "kylie-jenner" => esc_html__( "Kylie Jenner", "trx_addons" ),
				// "kyrasoundwaveai" => esc_html__( "Kyra_Soundwaveai", "trx_addons" ),
				// "l66ggg1713263520645" => esc_html__( "l66ggg_1713263520645", "trx_addons" ),
				// "l9dxefmh" => esc_html__( "L9DXEFMH", "trx_addons" ),
				// "lana-del-rey" => esc_html__( "lana-del-rey", "trx_addons" ),
				// "landonturbo" => esc_html__( "Landon_Turbo", "trx_addons" ),
				// "lebron-james" => esc_html__( "Lebron James", "trx_addons" ),
				// "lee-know" => esc_html__( "lee know", "trx_addons" ),
				// "lee-know-artingio" => esc_html__( "lee know arting.io", "trx_addons" ),
				// "leon-matt-mercier" => esc_html__( "Leon (Matt Mercier)", "trx_addons" ),
				// "leon-matt-mercier-artingi" => esc_html__( "Leon (Matt Mercier) arting.io", "trx_addons" ),
				// "leon-scott-kennedy-re4-re" => esc_html__( "Leon Scott Kennedy RE4 Remake", "trx_addons" ),
				// "leonardo-dicaprio" => esc_html__( "Leonardo DiCaprio", "trx_addons" ),
				// "liama2033656-9ea9-4ef8-96" => esc_html__( "Liama2033656-9ea9-4ef8-9616-7828996c1790", "trx_addons" ),
				// "lil-uzi-vert" => esc_html__( "Lil Uzi Vert", "trx_addons" ),
				// "lisa-simpson" => esc_html__( "lisa simpson", "trx_addons" ),
				// "lolo-mccv" => esc_html__( "LOLO MCCV", "trx_addons" ),
				// "longerversion171325452627" => esc_html__( "longerversion_1713254526278", "trx_addons" ),
				// "longvoice" => esc_html__( "longvoice", "trx_addons" ),
				// "lorne" => esc_html__( "Lorne", "trx_addons" ),
				// "lowpit-pix-vid" => esc_html__( "lowpit-pix-vid", "trx_addons" ),
				// "lucas88876dc4-e572-4b7f-b" => esc_html__( "Lucas88876dc4-e572-4b7f-bced-7bf3f23ee63f", "trx_addons" ),
				// "luffy-artingio" => esc_html__( "Luffy arting.io", "trx_addons" ),
				// "lula" => esc_html__( "lula", "trx_addons" ),
				// "macarena" => esc_html__( "Macarena", "trx_addons" ),
				// "macron" => esc_html__( "macron", "trx_addons" ),
				// "maechen" => esc_html__( "maechen", "trx_addons" ),
				// "mahesh-babu" => esc_html__( "mahesh babu", "trx_addons" ),
				// "male-deep-gravelly" => esc_html__( "Male - DEEP Gravelly", "trx_addons" ),
				// "maluma" => esc_html__( "maluma", "trx_addons" ),
				// "mandatocd86d37d-455e-4366" => esc_html__( "MANDATOcd86d37d-455e-4366-a66b-521b1ed00361", "trx_addons" ),
				// "marceline" => esc_html__( "Marceline", "trx_addons" ),
				// "marceline-artingio" => esc_html__( "Marceline arting.io", "trx_addons" ),
				// "marcos-jos-amadorc754fdfa" => esc_html__( "Marcos José Amadorc754fdfa-8721-4b67-bd81-2025f7372b38", "trx_addons" ),
				// "marge-simpson" => esc_html__( "marge simpson", "trx_addons" ),
				// "maria41ff2d5a-586c-48fb-a" => esc_html__( "Maria41ff2d5a-586c-48fb-a02f-582c30089788", "trx_addons" ),
				// "mariella" => esc_html__( "Mariella", "trx_addons" ),
				// "mario" => esc_html__( "Mario", "trx_addons" ),
				// "mario-artingio" => esc_html__( "Mario arting.io", "trx_addons" ),
				// "mario-model" => esc_html__( "Mario Model", "trx_addons" ),
				// "mario-model-artingio" => esc_html__( "Mario Model arting.io", "trx_addons" ),
				// "mario-more-accurate-artin" => esc_html__( "Mario (more accurate) arting.io", "trx_addons" ),
				// "mario-sml" => esc_html__( "Mario (SML)", "trx_addons" ),
				// "mario-sml-v0" => esc_html__( "Mario (SML) (V0)", "trx_addons" ),
				// "mario-uncompressed" => esc_html__( "Mario (uncompressed)", "trx_addons" ),
				// "mario-uncompressed-arting" => esc_html__( "Mario (uncompressed) arting.io", "trx_addons" ),
				// "markiplier" => esc_html__( "Markiplier", "trx_addons" ),
				// "markiplier-artingio" => esc_html__( "Markiplier arting.io", "trx_addons" ),
				// "markiplier-better" => esc_html__( "Markiplier (Better)", "trx_addons" ),
				// "mary-voice" => esc_html__( "Mary Voice", "trx_addons" ),
				// "math-1cb4f69f3-dcfb-4b2e-" => esc_html__( "Math 1cb4f69f3-dcfb-4b2e-895b-40eb2ec56253", "trx_addons" ),
				// "matheus3c9d459e-ff34-4693" => esc_html__( "Matheus3c9d459e-ff34-4693-8d04-8dd5065a5f3d", "trx_addons" ),
				// "matilda" => esc_html__( "Matilda", "trx_addons" ),
				// "matildaaf1752d4-7303-4029" => esc_html__( "Matildaaf1752d4-7303-4029-a5ba-d9cdd6aadb6e", "trx_addons" ),
				// "matteo" => esc_html__( "Matteo", "trx_addons" ),
				// "matthew1713256072569" => esc_html__( "matthew_1713256072569", "trx_addons" ),
				// "max" => esc_html__( "Max", "trx_addons" ),
				// "mbappe" => esc_html__( "mbappe", "trx_addons" ),
				// "mccv-voice" => esc_html__( "MCCV VOICE", "trx_addons" ),
				// "mcgregor" => esc_html__( "McGregor", "trx_addons" ),
				// "me" => esc_html__( "Me", "trx_addons" ),
				// "mee13cf1ff-9103-4f22-b63f" => esc_html__( "Mãee13cf1ff-9103-4f22-b63f-0f962415f372", "trx_addons" ),
				// "megan-2" => esc_html__( "MEGAN 2", "trx_addons" ),
				// "megan-lifewave" => esc_html__( "MEGAN LIFEWAVE", "trx_addons" ),
				// "meixi2" => esc_html__( "meixi2", "trx_addons" ),
				// "melee-fox-ver-2" => esc_html__( "Melee Fox Ver. 2", "trx_addons" ),
				// "melisa" => esc_html__( "melisa", "trx_addons" ),
				// "mercury" => esc_html__( "mercury", "trx_addons" ),
				// "messi" => esc_html__( "Messi", "trx_addons" ),
				// "meu16e139c04-d25f-4ca1-bc" => esc_html__( "Meu16e139c04-d25f-4ca1-bc74-3c9954ee4953", "trx_addons" ),
				// "meu28fc6205a-aa69-4952-aa" => esc_html__( "Meu28fc6205a-aa69-4952-aa59-f1025b9ce114", "trx_addons" ),
				// "mf" => esc_html__( "MF", "trx_addons" ),
				// "mi" => esc_html__( "Mi", "trx_addons" ),
				// "mi7ik3ds" => esc_html__( "MI7IK3DS", "trx_addons" ),
				// "michael-angelis" => esc_html__( "Michael Angelis", "trx_addons" ),
				// "michael-jackson-bad-dange" => esc_html__( "Michael Jackson Bad & Dangerous Era", "trx_addons" ),
				// "michael-jackson-ghosts-th" => esc_html__( "Michael Jackson Ghosts & Thriller Vocals", "trx_addons" ),
				// "michael-jackson-invincibl" => esc_html__( "Michael Jackson Invincible Era Raspy", "trx_addons" ),
				// "michael-jackson-re-upload" => esc_html__( "Michael Jackson (Re-Upload)", "trx_addons" ),
				// "miguel-durand" => esc_html__( "Miguel Durand", "trx_addons" ),
				// "migueld5081a60-3ec6-48dd-" => esc_html__( "Migueld5081a60-3ec6-48dd-bca3-9d680fa690cb", "trx_addons" ),
				// "mike-g" => esc_html__( "Mike G", "trx_addons" ),
				// "miley" => esc_html__( "Miley", "trx_addons" ),
				// "milton" => esc_html__( "Milton", "trx_addons" ),
				// "mine" => esc_html__( "mine", "trx_addons" ),
				// "minecraft-villager" => esc_html__( "Minecraft Villager", "trx_addons" ),
				// "minecraft-villager-arting" => esc_html__( "Minecraft Villager arting.io", "trx_addons" ),
				// "minecraft-villager-minecr" => esc_html__( "Minecraft Villager (Minecraft)", "trx_addons" ),
				// "minion" => esc_html__( "minion", "trx_addons" ),
				// "minionshapyy-birthday" => esc_html__( "Minions.hapyy Birthday", "trx_addons" ),
				// "minionshapyy-birthday-art" => esc_html__( "Minions.hapyy Birthday arting.io", "trx_addons" ),
				// "mith" => esc_html__( "Mith", "trx_addons" ),
				// "mlavoice1" => esc_html__( "mla_voice1", "trx_addons" ),
				// "mm-1d095ed44-6620-49cd-81" => esc_html__( "MM 1d095ed44-6620-49cd-811b-e9040bfa1f17", "trx_addons" ),
				// "mm-2b5afc39d-765a-47f4-b8" => esc_html__( "MM 2b5afc39d-765a-47f4-b867-f7dccbf5ffd0", "trx_addons" ),
				// "modi" => esc_html__( "Modi", "trx_addons" ),
				// "monga1e4087d0-3a23-4f4a-8" => esc_html__( "Monga1e4087d0-3a23-4f4a-860c-f75588fd9856", "trx_addons" ),
				// "monika" => esc_html__( "Monika", "trx_addons" ),
				// "monika-artingio" => esc_html__( "Monika arting.io", "trx_addons" ),
				// "morconn" => esc_html__( "Morconn", "trx_addons" ),
				// "mordecai" => esc_html__( "Mordecai", "trx_addons" ),
				// "mordecai-artingio" => esc_html__( "Mordecai arting.io", "trx_addons" ),
				// "mordecaiai" => esc_html__( "MordecaiAI", "trx_addons" ),
				// "moreira" => esc_html__( "moreira", "trx_addons" ),
				// "morgan" => esc_html__( "Morgan", "trx_addons" ),
				// "morgan-freeman" => esc_html__( "Morgan Freeman", "trx_addons" ),
				// "morgan-freeman-artingio" => esc_html__( "Morgan Freeman arting.io", "trx_addons" ),
				// "morgan-wright" => esc_html__( "Morgan Wright", "trx_addons" ),
				// "morgan_freeman" => esc_html__( "Morgan Freeman", "trx_addons" ),
				// "morgana-persona-5royal" => esc_html__( "Morgana (Persona 5/Royal)", "trx_addons" ),
				// "morganw-222" => esc_html__( "morganw 222", "trx_addons" ),
				// "moussavoice" => esc_html__( "moussavoice", "trx_addons" ),
				// "mozza1" => esc_html__( "mozza1", "trx_addons" ),
				// "mpnklt5x" => esc_html__( "MPNKLT5X", "trx_addons" ),
				// "mr-oxley" => esc_html__( "Mr. Oxley", "trx_addons" ),
				// "mrkrab" => esc_html__( "Mrkrab", "trx_addons" ),
				// "mundo-1" => esc_html__( "Mundo 1", "trx_addons" ),
				// "my-own-voice" => esc_html__( "My Own Voice", "trx_addons" ),
				// "my-super-voice" => esc_html__( "My Super Voice", "trx_addons" ),
				// "my-voice" => esc_html__( "My voice", "trx_addons" ),
				// "my-voice-is-first-and-sec" => esc_html__( "my voice is first and seconde", "trx_addons" ),
				// "nancy3a9d9aed-60fe-41ed-b" => esc_html__( "Nancy3a9d9aed-60fe-41ed-b5d0-d2120660dd39", "trx_addons" ),
				// "narendra-modi" => esc_html__( "Narendra Modi", "trx_addons" ),
				// "narendra-modi-artingio" => esc_html__( "Narendra Modi arting.io", "trx_addons" ),
				// "narrador-ncc7f9f26e-c7b7-" => esc_html__( "Narrador NCc7f9f26e-c7b7-4dd9-9ea5-d87816aea670", "trx_addons" ),
				// "naruto" => esc_html__( "naruto", "trx_addons" ),
				// "naruto-artingio" => esc_html__( "Naruto arting.io", "trx_addons" ),
				// "navyseals1713255750657" => esc_html__( "navyseals_1713255750657", "trx_addons" ),
				// "neal" => esc_html__( "Neal", "trx_addons" ),
				// "neal-helman" => esc_html__( "Neal Helman", "trx_addons" ),
				// "neco-arc" => esc_html__( "Neco Arc", "trx_addons" ),
				// "neco-arc-chaos" => esc_html__( "Neco Arc Chaos", "trx_addons" ),
				// "neco-arc-chaos-artingio" => esc_html__( "Neco Arc Chaos arting.io", "trx_addons" ),
				// "neco-arc-destiny" => esc_html__( "Neco Arc Destiny", "trx_addons" ),
				// "neha" => esc_html__( "Neha", "trx_addons" ),
				// "neon-man" => esc_html__( "Neon Man", "trx_addons" ),
				// "new" => esc_html__( "new", "trx_addons" ),
				// "new-1" => esc_html__( "New test-1", "trx_addons" ),
				// "new-voice" => esc_html__( "new voice", "trx_addons" ),
				// "new-voice-2" => esc_html__( "New Voice 2", "trx_addons" ),
				// "newyoek" => esc_html__( "newyoek", "trx_addons" ),
				// "neymarc168b041-dd9c-4773-" => esc_html__( "Neymarc168b041-dd9c-4773-86a2-9dfaa67e9f18", "trx_addons" ),
				// "ngvoice" => esc_html__( "ngvoice", "trx_addons" ),
				// "nicki" => esc_html__( "nicki", "trx_addons" ),
				// "nicolebb70d9a4-18de-4a9d-" => esc_html__( "Nicolebb70d9a4-18de-4a9d-bc58-2b811cf8f7f6", "trx_addons" ),
				// "niek" => esc_html__( "niek", "trx_addons" ),
				// "niitin" => esc_html__( "Niitin", "trx_addons" ),
				// "nikolai-voice-male1" => esc_html__( "Nikolai-voice-male1", "trx_addons" ),
				// "nmrjzi4o" => esc_html__( "NMRJZI4O", "trx_addons" ),
				// "nnbbccc1713258167276" => esc_html__( "nnbbccc_1713258167276", "trx_addons" ),
				// "nnzvzz1u" => esc_html__( "NNZVZZ1U", "trx_addons" ),
				// "noah" => esc_html__( "Noah", "trx_addons" ),
				// "null1713172473027" => esc_html__( "null_1713172473027", "trx_addons" ),
				// "null1713172579584" => esc_html__( "null_1713172579584", "trx_addons" ),
				// "obama" => esc_html__( "Barack Obama", "trx_addons" ),
				// "obama11713261673478" => esc_html__( "obama1_1713261673478", "trx_addons" ),
				// "obama1717654860901" => esc_html__( "obama_1717654860901", "trx_addons" ),
				// "obama21713261947478" => esc_html__( "obama2_1713261947478", "trx_addons" ),
				// "obama41713266001735" => esc_html__( "obama4_1713266001735", "trx_addons" ),
				// "oiuqy6ap" => esc_html__( "OIUQY6AP", "trx_addons" ),
				// "oliveirad48e9aa2-9e47-476" => esc_html__( "Oliveirad48e9aa2-9e47-4761-9c56-55bb3d8d960e", "trx_addons" ),
				// "oliviab1105e5c-a295-42e6-" => esc_html__( "Oliviab1105e5c-a295-42e6-9c25-946575b16ed0", "trx_addons" ),
				// "oogoo11713150073538" => esc_html__( "oogoo1_1713150073538", "trx_addons" ),
				// "oogoo2311713150222938" => esc_html__( "oogoo231_1713150222938", "trx_addons" ),
				// "oooo9o9o9" => esc_html__( "oooo9o9o9", "trx_addons" ),
				// "opgg1713261256280" => esc_html__( "opgg_1713261256280", "trx_addons" ),
				// "oprah_winfrey" => esc_html__( "oprah winfrey", "trx_addons" ),
				// "optimus-prime-artingio" => esc_html__( "Optimus Prime arting.io", "trx_addons" ),
				// "optimus-prime-v1-artingio" => esc_html__( "Optimus Prime v1 arting.io", "trx_addons" ),
				// "oxxx" => esc_html__( "oxxx", "trx_addons" ),
				// "oxxxymiron" => esc_html__( "oxxxymiron", "trx_addons" ),
				// "palade-deus625274a2-4937-" => esc_html__( "palade deus625274a2-4937-4f78-9fe0-319348027071", "trx_addons" ),
				// "pamala" => esc_html__( "Pamala", "trx_addons" ),
				// "patrick-chinese-1" => esc_html__( "Patrick-Chinese-1", "trx_addons" ),
				// "paty-buildbot" => esc_html__( "Paty-BuildBot", "trx_addons" ),
				// "paul" => esc_html__( "paul", "trx_addons" ),
				// "paul-2" => esc_html__( "Paul 2", "trx_addons" ),
				// "paul-p" => esc_html__( "Paul P", "trx_addons" ),
				// "paul-tearse" => esc_html__( "paul tearse", "trx_addons" ),
				// "paulinho-buildbot" => esc_html__( "Paulinho-BuildBot", "trx_addons" ),
				// "pedro" => esc_html__( "Pedro", "trx_addons" ),
				// "pedro-moscoso91097ce1-6f2" => esc_html__( "Pedro Moscoso91097ce1-6f29-4c49-83ce-85cf249bf14a", "trx_addons" ),
				// "peitaa" => esc_html__( "PEÑITAA", "trx_addons" ),
				// "pene" => esc_html__( "Pene", "trx_addons" ),
				// "percio7a389365-2fad-40f3-" => esc_html__( "Percio7a389365-2fad-40f3-b538-a9e1f5ac1648", "trx_addons" ),
				// "persona" => esc_html__( "PERSONA", "trx_addons" ),
				// "peter" => esc_html__( "Peter", "trx_addons" ),
				// "peter-griffin" => esc_html__( "Peter Griffin", "trx_addons" ),
				// "peter-griffin-artingio" => esc_html__( "Peter Griffin arting.io", "trx_addons" ),
				// "peter-griffin-deutsch" => esc_html__( "peter griffin deutsch", "trx_addons" ),
				// "peter-griffin-family-guy" => esc_html__( "Peter Griffin (Family Guy)", "trx_addons" ),
				// "peter-griffin-german-voic" => esc_html__( "Peter Griffin German Voice", "trx_addons" ),
				// "peter-wuttke" => esc_html__( "Peter Wuttke", "trx_addons" ),
				// "peter-wuttke-2" => esc_html__( "Peter Wuttke 2", "trx_addons" ),
				// "petrasoft" => esc_html__( "PetraSoft", "trx_addons" ),
				// "philip-harman" => esc_html__( "Philip Harman", "trx_addons" ),
				// "phone-guy" => esc_html__( "Phone Guy", "trx_addons" ),
				// "phone-guy-artingio" => esc_html__( "Phone Guy arting.io", "trx_addons" ),
				// "phone-guy-fnaf-2" => esc_html__( "Phone Guy (FNAF 2)", "trx_addons" ),
				// "phone-guy-voice-made-by-m" => esc_html__( "Phone Guy voice made by me!", "trx_addons" ),
				// "pierre" => esc_html__( "Pierre", "trx_addons" ),
				// "pierre-coovert" => esc_html__( "Pierre Coovert", "trx_addons" ),
				// "plankton-v2" => esc_html__( "Plankton V2", "trx_addons" ),
				// "plankton-v2-artingio" => esc_html__( "Plankton V2 arting.io", "trx_addons" ),
				// "pmw" => esc_html__( "pmw", "trx_addons" ),
				// "policial5e0bdbb9-f34c-405" => esc_html__( "policial5e0bdbb9-f34c-405e-aedd-b506f21d959c", "trx_addons" ),
				// "poo" => esc_html__( "poo", "trx_addons" ),
				// "pooh" => esc_html__( "pooh", "trx_addons" ),
				// "pop-smoke" => esc_html__( "pop-smoke", "trx_addons" ),
				// "popo" => esc_html__( "popo", "trx_addons" ),
				// "popoq" => esc_html__( "popoq", "trx_addons" ),
				// "popoqsds" => esc_html__( "popoqsds", "trx_addons" ),
				// "post-malone" => esc_html__( "Post Malone", "trx_addons" ),
				// "ppp1713248067254" => esc_html__( "ppp_1713248067254", "trx_addons" ),
				// "pr" => esc_html__( "pr", "trx_addons" ),
				// "presona-20cefc5b4-b893-4d" => esc_html__( "presona 20cefc5b4-b893-4dbf-bbc7-d4f5f62c412c", "trx_addons" ),
				// "q1jx3e06" => esc_html__( "Q1JX3E06", "trx_addons" ),
				// "q4hcpsa9" => esc_html__( "Q4HCPSA9", "trx_addons" ),
				// "queene" => esc_html__( "QueenE", "trx_addons" ),
				// "quiz-aventura-no-apagarf4" => esc_html__( "QUIZ AVENTURA NÃO APAGARf417e8b3-f6ad-4dad-b333-ae2a280f0430", "trx_addons" ),
				// "qwerr" => esc_html__( "qwerr", "trx_addons" ),
				// "r9o62pcl" => esc_html__( "R9O62PCL", "trx_addons" ),
				// "rafaelef1c89a1-1e00-4e54-" => esc_html__( "Rafaelef1c89a1-1e00-4e54-8a00-3d1e588e1bff", "trx_addons" ),
				// "raiamadfd4c55-4190-4265-b" => esc_html__( "Raiamadfd4c55-4190-4265-bfc6-6ed7b9647ce9", "trx_addons" ),
				// "raiame20e9789-43c9-455c-9" => esc_html__( "Raiame20e9789-43c9-455c-911a-db208fbf9b3e", "trx_addons" ),
				// "raineraca807da-a58e-493e-" => esc_html__( "Raineraca807da-a58e-493e-beb3-7d3593285ae0", "trx_addons" ),
				// "raj" => esc_html__( "Raj", "trx_addons" ),
				// "rami" => esc_html__( "rami", "trx_addons" ),
				// "randall-monk" => esc_html__( "Randall Monk", "trx_addons" ),
				// "raquel1f60c60f-c545-48dd-" => esc_html__( "Raquel1f60c60f-c545-48dd-9d43-d957f6a928e9", "trx_addons" ),
				// "raul5c23cc4d-9346-4dfd-9e" => esc_html__( "Raul5c23cc4d-9346-4dfd-9ea1-39e5e83ff050", "trx_addons" ),
				// "rbjvoice" => esc_html__( "RBJ_voice", "trx_addons" ),
				// "renan-alvesea275173-fd4a-" => esc_html__( "Renan Alvesea275173-fd4a-46d1-a0ae-4df77f91e720", "trx_addons" ),
				// "rene" => esc_html__( "Rene", "trx_addons" ),
				// "rhovenabb0dd6c7-a335-4256" => esc_html__( "rhovenabb0dd6c7-a335-4256-960f-e0c92da5c07e", "trx_addons" ),
				// "rico" => esc_html__( "Rico", "trx_addons" ),
				// "riggy-the-runkey" => esc_html__( "Riggy the runkey", "trx_addons" ),
				// "riggy-the-runkey-artingio" => esc_html__( "Riggy the runkey arting.io", "trx_addons" ),
				// "rihanna" => esc_html__( "Rihanna", "trx_addons" ),
				// "rimuru" => esc_html__( "Rimuru", "trx_addons" ),
				// "rlg" => esc_html__( "RLG", "trx_addons" ),
				// "robert661713250184869" => esc_html__( "robert66_1713250184869", "trx_addons" ),
				// "roberto" => esc_html__( "Roberto", "trx_addons" ),
				// "rodrigo-albuquerquefb8f6f" => esc_html__( "Rodrigo Albuquerquefb8f6f5b-0672-49e9-aa8f-e953b68ffb28", "trx_addons" ),
				// "rodrigo-goes8856d3ed-2d12" => esc_html__( "rodrigo goes8856d3ed-2d12-48f2-a3a5-2ec69e5eab39", "trx_addons" ),
				// "rohn" => esc_html__( "Rohn", "trx_addons" ),
				// "ronaldo" => esc_html__( "Ronaldo", "trx_addons" ),
				// "ronaldo-artingio" => esc_html__( "Ronaldo arting.io", "trx_addons" ),
				// "ronaldoorignal" => esc_html__( "ronaldoorignal", "trx_addons" ),
				// "ronlado-silvaac55dbf4-399" => esc_html__( "Ronlado Silvaac55dbf4-399a-4566-8e8f-22daa49951ea", "trx_addons" ),
				// "roro" => esc_html__( "RORO", "trx_addons" ),
				// "rosamund-pike" => esc_html__( "Rosamund Pike", "trx_addons" ),
				// "rr-1" => esc_html__( "RR 1", "trx_addons" ),
				// "rtjrtjrjrtj" => esc_html__( "rtjrtjrjrtj", "trx_addons" ),
				// "ruryu" => esc_html__( "ruryu", "trx_addons" ),
				// "russel8ec2b832-fabf-4f63-" => esc_html__( "Russel8ec2b832-fabf-4f63-8670-05b579ab4e6f", "trx_addons" ),
				// "russell" => esc_html__( "Russell", "trx_addons" ),
				// "russell-reeves" => esc_html__( "Russell Reeves", "trx_addons" ),
				// "s1" => esc_html__( "S1", "trx_addons" ),
				// "s1qe60r7" => esc_html__( "S1QE60R7", "trx_addons" ),
				// "s2" => esc_html__( "S2", "trx_addons" ),
				// "s6wy2kg1" => esc_html__( "S6WY2KG1", "trx_addons" ),
				// "s9ph5vg1" => esc_html__( "S9PH5VG1", "trx_addons" ),
				// "sab01" => esc_html__( "SAB01", "trx_addons" ),
				// "sabrina-buildbot" => esc_html__( "Sabrina-BuildBot", "trx_addons" ),
				// "sadaka" => esc_html__( "Sadaka", "trx_addons" ),
				// "sadasdsd23wsa" => esc_html__( "sadasdsd23wsa", "trx_addons" ),
				// "said-english1" => esc_html__( "Said-English1", "trx_addons" ),
				// "said-englishuk1" => esc_html__( "Said-EnglishUK1", "trx_addons" ),
				// "said-uk-english" => esc_html__( "Said-UK-English", "trx_addons" ),
				// "salva-1" => esc_html__( "Salva 1", "trx_addons" ),
				// "sam" => esc_html__( "Sam", "trx_addons" ),
				// "santa-claus688dd088-daa9-" => esc_html__( "Santa Claus688dd088-daa9-45e5-9c50-99347a7c190b", "trx_addons" ),
				// "sarah4d6241dc-bc8f-46c8-a" => esc_html__( "Sarah4d6241dc-bc8f-46c8-a800-a7e5ed4ef8f1", "trx_addons" ),
				// "sawthesizer" => esc_html__( "SAWthesizer", "trx_addons" ),
				// "sawthesizer2" => esc_html__( "SAWthesizer2", "trx_addons" ),
				// "scott" => esc_html__( "scott", "trx_addons" ),
				// "scott1713001646344" => esc_html__( "scott_1713001646344", "trx_addons" ),
				// "scott1713001712513" => esc_html__( "scott_1713001712513", "trx_addons" ),
				// "scott1713002066825" => esc_html__( "scott_1713002066825", "trx_addons" ),
				// "scott1713002206015" => esc_html__( "scott_1713002206015", "trx_addons" ),
				// "scott1713002283659" => esc_html__( "scott_1713002283659", "trx_addons" ),
				// "scott1713002387909" => esc_html__( "scott_1713002387909", "trx_addons" ),
				// "scott1713002445022" => esc_html__( "scott_1713002445022", "trx_addons" ),
				// "scott1713002794431" => esc_html__( "scott_1713002794431", "trx_addons" ),
				// "scott1713002857411" => esc_html__( "scott_1713002857411", "trx_addons" ),
				// "scott1713003001758" => esc_html__( "scott_1713003001758", "trx_addons" ),
				// "scott1713003034471" => esc_html__( "scott_1713003034471", "trx_addons" ),
				// "scott1713003156066" => esc_html__( "scott_1713003156066", "trx_addons" ),
				// "scott1713003291022" => esc_html__( "scott_1713003291022", "trx_addons" ),
				// "scott1713003912219" => esc_html__( "scott_1713003912219", "trx_addons" ),
				// "scott1713004086348" => esc_html__( "scott_1713004086348", "trx_addons" ),
				// "scott1713234776820" => esc_html__( "scott_1713234776820", "trx_addons" ),
				// "scott1713235031853" => esc_html__( "scott_1713235031853", "trx_addons" ),
				// "scott1713235256728" => esc_html__( "scott_1713235256728", "trx_addons" ),
				// "scott1713235284640" => esc_html__( "scott_1713235284640", "trx_addons" ),
				// "scottb1713258926413" => esc_html__( "scottb_1713258926413", "trx_addons" ),
				// "scottq1713258488537" => esc_html__( "scottq_1713258488537", "trx_addons" ),
				// "scottq1713258875693" => esc_html__( "scottq_1713258875693", "trx_addons" ),
				// "scsc1231713154110181" => esc_html__( "scsc123_1713154110181", "trx_addons" ),
				// "scsc1713152595236" => esc_html__( "scsc_1713152595236", "trx_addons" ),
				// "scsc233331713154295229" => esc_html__( "scsc23333_1713154295229", "trx_addons" ),
				// "scsc233331713154348786" => esc_html__( "scsc23333_1713154348786", "trx_addons" ),
				// "seen1" => esc_html__( "seen1", "trx_addons" ),
				// "selena-gomez" => esc_html__( "Selena Gomez", "trx_addons" ),
				// "selenagomezxa" => esc_html__( "selenagomezxa", "trx_addons" ),
				// "sexy" => esc_html__( "sexy", "trx_addons" ),
				// "sfera-ebbasta" => esc_html__( "Sfera Ebbasta", "trx_addons" ),
				// "sfera-ebbasta-artingio" => esc_html__( "Sfera Ebbasta arting.io", "trx_addons" ),
				// "sg-dxcv" => esc_html__( "SG dxcv", "trx_addons" ),
				// "sg-fsdf-j" => esc_html__( "SG fsdf J", "trx_addons" ),
				// "sg-fsdf-j-63" => esc_html__( "SG fsdf J 63", "trx_addons" ),
				// "sg-fsdf-j-64" => esc_html__( "SG fsdf J 64", "trx_addons" ),
				// "sg-fsdf-j-65" => esc_html__( "SG fsdf J 65", "trx_addons" ),
				// "sg-fsdf-j-66" => esc_html__( "SG fsdf J 66", "trx_addons" ),
				// "sg-fsdf-j-6sadsadasdasd3" => esc_html__( "SG fsdf J 6sadsadasdasd3", "trx_addons" ),
				// "shahrukh" => esc_html__( "shahrukh", "trx_addons" ),
				// "shahrukh-khan" => esc_html__( "Shahrukh khan", "trx_addons" ),
				// "shahrukh-voice" => esc_html__( "Shahrukh voice", "trx_addons" ),
				// "shakira" => esc_html__( "shakira", "trx_addons" ),
				// "shaz-ar" => esc_html__( "shaz-ar", "trx_addons" ),
				// "shortvoice" => esc_html__( "shortvoice", "trx_addons" ),
				// "sidhu-moose" => esc_html__( "Sidhu Moose", "trx_addons" ),
				// "silasdf20abd0-737d-4497-b" => esc_html__( "SILASdf20abd0-737d-4497-b686-cfdc8ef06d7c", "trx_addons" ),
				// "simon" => esc_html__( "Simon", "trx_addons" ),
				// "simone51ad4fbc-411d-4781-" => esc_html__( "simone51ad4fbc-411d-4781-960d-87aaa9fbfe57", "trx_addons" ),
				// "sir-david-attenborough" => esc_html__( "Sir David Attenborough", "trx_addons" ),
				// "sir-david-attenborough-ar" => esc_html__( "Sir David Attenborough arting.io", "trx_addons" ),
				// "slc-2" => esc_html__( "slc-2", "trx_addons" ),
				// "sljackson" => esc_html__( "SLJackson", "trx_addons" ),
				// "snhmjead" => esc_html__( "SNHMJEAD", "trx_addons" ),
				// "snoop_dogg" => esc_html__( "snoop dogg", "trx_addons" ),
				// "socorro" => esc_html__( "Socorro", "trx_addons" ),
				// "song-1" => esc_html__( "Song 1", "trx_addons" ),
				// "sonic-the-hedgehog-arting" => esc_html__( "Sonic The Hedgehog arting.io", "trx_addons" ),
				// "sonny3" => esc_html__( "Sonny3", "trx_addons" ),
				// "sonny4" => esc_html__( "Sonny4", "trx_addons" ),
				// "soy-luna" => esc_html__( "SOY LUNA", "trx_addons" ),
				// "spiritual" => esc_html__( "spiritual", "trx_addons" ),
				// "spongebob-1000" => esc_html__( "SpongeBob (1000)", "trx_addons" ),
				// "srk" => esc_html__( "srk", "trx_addons" ),
				// "srk-sample" => esc_html__( "SRK-sample", "trx_addons" ),
				// "ss" => esc_html__( "ss", "trx_addons" ),
				// "ssss1713172170980" => esc_html__( "ssss_1713172170980", "trx_addons" ),
				// "ssss1713172303794" => esc_html__( "ssss_1713172303794", "trx_addons" ),
				// "ssss1713172645038" => esc_html__( "ssss_1713172645038", "trx_addons" ),
				// "ssss1713172738974" => esc_html__( "ssss_1713172738974", "trx_addons" ),
				// "ste" => esc_html__( "ste", "trx_addons" ),
				// "stefnao-dioccane" => esc_html__( "Stefnao dioccane", "trx_addons" ),
				// "stevejobs" => esc_html__( "SteveJobs", "trx_addons" ),
				// "stevejobs11713259508696" => esc_html__( "stevejobs1_1713259508696", "trx_addons" ),
				// "stevejobs21713261560072" => esc_html__( "stevejobs2_1713261560072", "trx_addons" ),
				// "stolas-goetia" => esc_html__( "Stolas Goetia", "trx_addons" ),
				// "stolas-goetia-artingio" => esc_html__( "Stolas Goetia arting.io", "trx_addons" ),
				// "subhjoshi" => esc_html__( "subhjoshi", "trx_addons" ),
				// "sunliucui" => esc_html__( "sunliucui", "trx_addons" ),
				// "sup-1" => esc_html__( "TesT Sup 1", "trx_addons" ),
				// "super-mario-bros-wonder-t" => esc_html__( "Super Mario Bros Wonder - Talking Flower", "trx_addons" ),
				// "sv" => esc_html__( "SV", "trx_addons" ),
				// "t3d6wqn5" => esc_html__( "T3D6WQN5", "trx_addons" ),
				// "tanis" => esc_html__( "tanis", "trx_addons" ),
				// "tanjiro" => esc_html__( "Tanjiro", "trx_addons" ),
				// "tanjiro-artingio" => esc_html__( "Tanjiro arting.io", "trx_addons" ),
				// "tassio" => esc_html__( "tassio", "trx_addons" ),
				// "tedasfdasfsa8f642e16-9ea9" => esc_html__( "tedasfdasfsa8f642e16-9ea9-4962-b5a8-f3909f499015", "trx_addons" ),
				// "test" => esc_html__( "test", "trx_addons" ),
				// "text-support-2" => esc_html__( "Text Support 2", "trx_addons" ),
				// "thales-1a6313a2c-98ec-4fc" => esc_html__( "THALES 1a6313a2c-98ec-4fcc-9723-6e96258d7815", "trx_addons" ),
				// "thales-267534b1f-a653-415" => esc_html__( "THALES 267534b1f-a653-4152-8a31-ada5c374a253", "trx_addons" ),
				// "the-minions" => esc_html__( "The Minions", "trx_addons" ),
				// "the-minions-artingio" => esc_html__( "The Minions arting.io", "trx_addons" ),
				// "the-rock" => esc_html__( "The Rock", "trx_addons" ),
				// "the-voice-mr" => esc_html__( "The voice MR", "trx_addons" ),
				// "the-weekend" => esc_html__( "The Weekend", "trx_addons" ),
				// "thiago-finch85b823f4-4f4f" => esc_html__( "Thiago Finch85b823f4-4f4f-45e6-b5ad-07c3020be239", "trx_addons" ),
				// "thiago75e7c592-c1fc-4adf-" => esc_html__( "Thiago75e7c592-c1fc-4adf-b510-7b46d307fbcc", "trx_addons" ),
				// "tjz2jzlh" => esc_html__( "TJZ2JZLH", "trx_addons" ),
				// "toad" => esc_html__( "Toad", "trx_addons" ),
				// "toad-artingio" => esc_html__( "Toad arting.io", "trx_addons" ),
				// "toliver" => esc_html__( "toliver", "trx_addons" ),
				// "tom-holland" => esc_html__( "Tom Holland", "trx_addons" ),
				// "tom-holland-artingio" => esc_html__( "Tom Holland arting.io", "trx_addons" ),
				// "tom_hank" => esc_html__( "Tom Hank", "trx_addons" ),
				// "tony-boy" => esc_html__( "tony boy", "trx_addons" ),
				// "tony2" => esc_html__( "Tony2", "trx_addons" ),
				// "tote" => esc_html__( "Tote", "trx_addons" ),
				// "tovino" => esc_html__( "tovino", "trx_addons" ),
				// "tpvoice" => esc_html__( "TP_voice", "trx_addons" ),
				// "travis-scott" => esc_html__( "Travis Scott", "trx_addons" ),
				// "travisscotorign" => esc_html__( "travisscotorign", "trx_addons" ),
				// "trent-michael" => esc_html__( "Trent Michael", "trx_addons" ),
				// "trevor-philips" => esc_html__( "Trevor Philips", "trx_addons" ),
				// "trevor-philips-artingio" => esc_html__( "Trevor Philips arting.io", "trx_addons" ),
				// "trump-california" => esc_html__( "Trump California", "trx_addons" ),
				// "trump001" => esc_html__( "TRUMP001", "trx_addons" ),
				// "trumpel" => esc_html__( "Trumpel", "trx_addons" ),
				// "trumpo-speach" => esc_html__( "Trumpo-speach", "trx_addons" ),
				// "trumpty" => esc_html__( "Trumpty", "trx_addons" ),
				// "tupac" => esc_html__( "tupac", "trx_addons" ),
				// "twilight-sparkle" => esc_html__( "Twilight Sparkle", "trx_addons" ),
				// "twilight-sparkle-artingio" => esc_html__( "Twilight Sparkle arting.io", "trx_addons" ),
				// "tylerswift" => esc_html__( "TylerSwift", "trx_addons" ),
				// "tyx5zrev" => esc_html__( "TYX5ZREV", "trx_addons" ),
				// "u2ny4o5h" => esc_html__( "U2NY4O5H", "trx_addons" ),
				// "u51th850" => esc_html__( "U51TH850", "trx_addons" ),
				// "u9fynzry" => esc_html__( "U9FYNZRY", "trx_addons" ),
				// "ucdada1713264761645" => esc_html__( "ucdada_1713264761645", "trx_addons" ),
				// "ugecoh6k" => esc_html__( "UGECOH6K", "trx_addons" ),
				// "ujzee103" => esc_html__( "UJZEE103", "trx_addons" ),
				// "uojhtpqa" => esc_html__( "UOJHTPQA", "trx_addons" ),
				// "ur6yca7p" => esc_html__( "UR6YCA7P", "trx_addons" ),
				// "uwe9p56y" => esc_html__( "UWE9P56Y", "trx_addons" ),
				// "uwt93ht4" => esc_html__( "UWT93HT4", "trx_addons" ),
				// "v" => esc_html__( "v", "trx_addons" ),
				// "v4" => esc_html__( "v4", "trx_addons" ),
				// "valentino" => esc_html__( "Valentino", "trx_addons" ),
				// "valentino-jo65c4d5a5-444d" => esc_html__( "VALENTINO - JO65c4d5a5-444d-4c2a-a6a9-99cdb42faaeb", "trx_addons" ),
				// "valerie" => esc_html__( "Valerie", "trx_addons" ),
				// "valerio" => esc_html__( "valerio", "trx_addons" ),
				// "vanessa" => esc_html__( "Vanessa", "trx_addons" ),
				// "vegeta" => esc_html__( "Vegeta", "trx_addons" ),
				// "vegeta-artingio" => esc_html__( "Vegeta arting.io", "trx_addons" ),
				// "velentino" => esc_html__( "Velentino", "trx_addons" ),
				// "veter" => esc_html__( "veter", "trx_addons" ),
				// "vicente" => esc_html__( "Vicente", "trx_addons" ),
				// "vicente79d5cc70-d231-4cd8" => esc_html__( "Vicente79d5cc70-d231-4cd8-875b-921a6a12a115", "trx_addons" ),
				// "victor" => esc_html__( "victor", "trx_addons" ),
				// "victor-emanuelf7c3f32e-35" => esc_html__( "Victor Emanuelf7c3f32e-35f3-4dca-9c7c-a3c1135b9182", "trx_addons" ),
				// "victor-jose-t" => esc_html__( "Victor Jose - T", "trx_addons" ),
				// "villager" => esc_html__( "villager", "trx_addons" ),
				// "villager-news" => esc_html__( "Villager News", "trx_addons" ),
				// "villager-news-artingio" => esc_html__( "Villager News arting.io", "trx_addons" ),
				// "villager-sound" => esc_html__( "villager sound", "trx_addons" ),
				// "vinay1" => esc_html__( "Vinay1", "trx_addons" ),
				// "vitria-fonseca8828f8ce-59" => esc_html__( "Vitória Fonseca8828f8ce-5999-4209-93ec-17d1d0bf796b", "trx_addons" ),
				// "vj" => esc_html__( "VJ", "trx_addons" ),
				// "vladimir-putin" => esc_html__( "Vladimir Putin", "trx_addons" ),
				// "vladimir-zelensky" => esc_html__( "Vladimir Zelensky", "trx_addons" ),
				// "vn4wp9vv" => esc_html__( "VN4WP9VV", "trx_addons" ),
				// "vocalis" => esc_html__( "Vocalis", "trx_addons" ),
				// "voice" => esc_html__( "voice", "trx_addons" ),
				// "voice1" => esc_html__( "Voice1", "trx_addons" ),
				// "voka" => esc_html__( "Voka", "trx_addons" ),
				// "voz-de-asdadssadzdsdsredf" => esc_html__( "voz de asdadssadzdsdsredfdgfzzz", "trx_addons" ),
				// "voz-de-e" => esc_html__( "voz de teste", "trx_addons" ),
				// "voz-de-zdsdsredfdgfzzz" => esc_html__( "voz de zdsdsredfdgfzzz", "trx_addons" ),
				// "voz-de-zzzz" => esc_html__( "voz de zzzz", "trx_addons" ),
				// "voz-nova" => esc_html__( "voz nova", "trx_addons" ),
				// "voz-valentinoea94b5bb-8b4" => esc_html__( "Voz Valentinoea94b5bb-8b41-4585-9880-9434c095d360", "trx_addons" ),
				// "vozffff2f0b7f8-aad6-45dd-" => esc_html__( "vozffff2f0b7f8-aad6-45dd-b00f-773d7a076f76", "trx_addons" ),
				// "vozffffff71f5dea0-793b-40" => esc_html__( "VOZFFFFFF71f5dea0-793b-40ac-93a3-79666443b64c", "trx_addons" ),
				// "vozrtp" => esc_html__( "vozrtp", "trx_addons" ),
				// "vp" => esc_html__( "vp", "trx_addons" ),
				// "vt6d37c4a2-70a9-432b-99a3" => esc_html__( "VT6d37c4a2-70a9-432b-99a3-3b5b0b2749c3", "trx_addons" ),
				// "vttt80469d64-acc6-4b8a-87" => esc_html__( "VTTT80469d64-acc6-4b8a-878f-0d9d2b68fe91", "trx_addons" ),
				// "w2mttnwz" => esc_html__( "W2MTTNWZ", "trx_addons" ),
				// "walter-white" => esc_html__( "Walter White", "trx_addons" ),
				// "walter-white-artingio" => esc_html__( "Walter White arting.io", "trx_addons" ),
				// "walter-white-breaking-bad" => esc_html__( "Walter White (Breaking Bad)", "trx_addons" ),
				// "wavelabs" => esc_html__( "WaveLabs", "trx_addons" ),
				// "waves" => esc_html__( "Waves", "trx_addons" ),
				// "web111" => esc_html__( "web111", "trx_addons" ),
				// "weekendorig" => esc_html__( "weekendorig", "trx_addons" ),
				// "wefwgwg" => esc_html__( "wefwgwg", "trx_addons" ),
				// "werasdf23" => esc_html__( "werasdf23", "trx_addons" ),
				// "werasdf24" => esc_html__( "werasdf24", "trx_addons" ),
				// "wesley-accent-da" => esc_html__( "Wesley Accent - DA", "trx_addons" ),
				// "wesley-rolton" => esc_html__( "Wesley Rolton", "trx_addons" ),
				// "wfc-optimus-prime-v1" => esc_html__( "WFC Optimus Prime v1", "trx_addons" ),
				// "when-income-just-isnt-eno" => esc_html__( "When Income just isn´t enough", "trx_addons" ),
				// "wigoffd4d6d1-6c49-46f9-b1" => esc_html__( "wigoffd4d6d1-6c49-46f9-b1bd-114f1c1626b4", "trx_addons" ),
				// "wilbur-soot" => esc_html__( "Wilbur Soot", "trx_addons" ),
				// "wilbur-soot-artingio" => esc_html__( "Wilbur Soot arting.io", "trx_addons" ),
				// "wilian-boner" => esc_html__( "Wilian Boner", "trx_addons" ),
				// "wilian-boners" => esc_html__( "Wilian Boners", "trx_addons" ),
				// "will-smith" => esc_html__( "Will Smith", "trx_addons" ),
				// "willie-david" => esc_html__( "Willie David", "trx_addons" ),
				// "wohu1713255568048" => esc_html__( "wohu_1713255568048", "trx_addons" ),
				// "wonyoungeeee" => esc_html__( "wonyoungeeee", "trx_addons" ),
				// "wood" => esc_html__( "Wood", "trx_addons" ),
				// "woow1713176053660" => esc_html__( "woow_1713176053660", "trx_addons" ),
				// "woow1713176138443" => esc_html__( "woow_1713176138443", "trx_addons" ),
				// "woow1713240483215" => esc_html__( "woow_1713240483215", "trx_addons" ),
				// "woow1713240605442" => esc_html__( "woow_1713240605442", "trx_addons" ),
				// "woshishuaige1713261436551" => esc_html__( "woshishuaige_1713261436551", "trx_addons" ),
				// "wowowofff1713258344476" => esc_html__( "wowowofff_1713258344476", "trx_addons" ),
				// "wrwerwer" => esc_html__( "wrwerwer", "trx_addons" ),
				// "x9zixuii" => esc_html__( "X9ZIXUII", "trx_addons" ),
				// "xfzoj61s" => esc_html__( "XFZOJ61S", "trx_addons" ),
				// "xiaog1713263282488" => esc_html__( "xiaog_1713263282488", "trx_addons" ),
				// "xmasfather" => esc_html__( "Xmas_Father", "trx_addons" ),
				// "xuiss" => esc_html__( "Xuiss", "trx_addons" ),
				// "xycgi619" => esc_html__( "XYCGI619", "trx_addons" ),
				// "xyz" => esc_html__( "xyz", "trx_addons" ),
				// "y50yscwk" => esc_html__( "Y50YSCWK", "trx_addons" ),
				// "y529k8rg" => esc_html__( "Y529K8RG", "trx_addons" ),
				// "yadayah" => esc_html__( "Yadayah", "trx_addons" ),
				// "yo-yo" => esc_html__( "Yo Yo", "trx_addons" ),
				// "ysx42eps" => esc_html__( "YSX42EPS", "trx_addons" ),
				// "yt-girl" => esc_html__( "YT Girl", "trx_addons" ),
				// "yt-girl-2" => esc_html__( "YT Girl 2", "trx_addons" ),
				// "yv" => esc_html__( "yv", "trx_addons" ),
				// "z9n83kiw" => esc_html__( "Z9N83KIW", "trx_addons" ),
				// "zac" => esc_html__( "zac", "trx_addons" ),
				// "zeta" => esc_html__( "Zeta", "trx_addons" ),
				// "zinn" => esc_html__( "zinn", "trx_addons" ),
				// "zkpjv9ln" => esc_html__( "ZKPJV9LN", "trx_addons" ),
				// "zoro" => esc_html__( "Zoro", "trx_addons" ),
				// "zoro-artingio" => esc_html__( "Zoro arting.io", "trx_addons" ),
				// "zuckerberg" => esc_html__( "zuckerberg", "trx_addons" ),
				// "zzzzzzzzz" => esc_html__( "zzzzzzzzz", "trx_addons" ),
			) );
		}

	}
}
