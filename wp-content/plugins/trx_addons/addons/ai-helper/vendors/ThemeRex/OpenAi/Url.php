<?php

namespace ThemeRex\OpenAi;

class Url {

	public const ORIGIN = 'https://api.openai.com';
	public const SITE = 'https://beta.openai.com';
	public const API_VERSION = 'v1';

	/**
	 * @return string
	 */
	public static function baseURL( $endpoint = '', $type = 'api' ): string {
		return ( $type == 'api' ? self::ORIGIN . "/" . self::API_VERSION : self::SITE ) . ( ! empty( $endpoint ) ? "/{$endpoint}" : '' );
	}

	/**
	 * @param string $engine
	 * 
	 * @return string
	 * 
	 * @deprecated
	 */
	public static function completionURL( string $engine ): string {
		return self::baseURL( "engines/{$engine}/completions" );
	}

	/**
	 * @return string
	 */
	public static function completionsURL(): string {
		return self::baseURL( "completions" );
	}

	/**
	 *
	 * @return string
	 */
	public static function editsUrl(): string {
		return self::baseURL( "edits" );
	}

	/**
	 * @param string $engine
	 * 
	 * @return string
	 */
	public static function searchURL( string $engine ): string {
		return self::baseURL( "engines/{$engine}/search" );
	}

	/**
	 * @return string
	 */
	public static function enginesUrl(): string {
		return self::baseURL( "engines" );
	}

	/**
	 * @param string $engine
	 * 
	 * @return string
	 */
	public static function engineUrl( string $engine ): string {
		return self::baseURL( "engines/{$engine}" );
	}

	/**
	 * @return string
	 */
	public static function classificationsUrl(): string {
		return self::baseURL( "classifications" );
	}

	/**
	 * @return string
	 */
	public static function moderationUrl(): string {
		return self::baseURL( "moderations" );
	}

	/**
	 * @return string
	 */
	public static function speechUrl(): string {
		return self::baseURL( "audio/speech" );
	}

	/**
	 * @return string
	 */
	public static function transcriptionsUrl(): string {
		return self::baseURL( "audio/transcriptions" );
	}

	/**
	 * @return string
	 */
	public static function translationsUrl(): string {
		return self::baseURL( "audio/translations" );
	}

	/**
	 * @return string
	 */
	public static function filesUrl(): string {
		return self::baseURL( "files" );
	}

	/**
	 * @return string
	 */
	public static function fineTuneUrl(): string {
		return self::baseURL( "fine-tunes" );
	}

	/**
	 * @return string
	 */
	public static function fineTuneModel(): string {
		return self::baseURL( "models" );
	}

	/**
	 * @return string
	 */
	public static function answersUrl(): string {
		return self::baseURL( "answers" );
	}

	/**
	 * @return string
	 */
	public static function imageUrl(): string {
		return self::baseURL( "images" );
	}

	/**
	 * @return string
	 */
	public static function embeddings(): string {
		return self::baseURL( "embeddings" );
	}

	/**
	 * @return string
	 */
	public static function chatUrl(): string {
		return self::baseURL( "chat/completions" );
	}

}
