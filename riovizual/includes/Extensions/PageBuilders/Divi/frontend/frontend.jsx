import { Component } from 'react';

class RiovizualSelector extends Component {

	static slug = 'riovizual_module';

	constructor( props ) {

		super( props );

		this.state = {
			error: null,
			isLoading: true,
			table: null,
			style: null,
		};

	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.riovizual !== this.props.riovizual ) {
			this.componentDidMount();
		}
	}

	componentDidMount() {
		const formData = new FormData();

		formData.append( 'nonce', riovizual_divi_builder.nonce );
		formData.append( 'action', 'riovizual_divi_preview' ); 
		formData.append( 'riovizual', this.props.riovizual );

		fetch(
			riovizual_divi_builder.ajax_url,
			{
				method: 'POST',
				cache: 'no-cache',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
					'Cache-Control': 'no-cache',
				},
				body: new URLSearchParams( formData ),
			},
		)
			.then( ( res ) => res.json() )
			.then(
				( result ) => {
					if (result.data.css) {
						// add style in head
						const styleElement = document.createElement('style');
						styleElement.textContent = result.data.css;
						document.head.appendChild(styleElement);
					}

					this.setState( {
						isLoading: false,
						table: result.data.content,
						style: result.data.css
					} );
				},
				( error ) => {
					this.setState( {
						isLoading: false,
						error,
					} );
				},
			);

	}

	render() {
		const { error, isLoaded, table } = this.state;

		if ( typeof this.props.riovizual === 'undefined' || this.props.riovizual === '' ) {
			return (
				<div className="riovizual-divi-empty-block">
					{ <p dangerouslySetInnerHTML={ { __html: riovizual_divi_builder.block_empty_text } } /> }

					<button type="button" onClick={
						() => {
							window.open( riovizual_divi_builder.get_started_url, '_blank' );
						}
					}
					>
						{ riovizual_divi_builder.get_started_text }
					</button>
				</div>
			);
		}

		if ( error || ! table ) {
			return (
				<div className="riovizual-divi-table-placeholder">
					<div><img src="https://riovizual.com/wp-content/uploads/2025/05/rio-icon-elementor.png" alt="" /> Please choose a table</div>		
				</div>
			);
		}

		return (
			<div className="riovizual-divi-table-preview">
				{ <div dangerouslySetInnerHTML={ { __html: table } } /> }
			</div>
		);
	}
}

jQuery( window )
	// Register custom modules.
	.on( 'et_builder_api_ready', ( event, API ) => {
		API.registerModules( [ RiovizualSelector ] );
	} )

	// Re-initialize riovizual frontend.
	.on( 'riovizualDiviModuleDisplay', () => {
		window.riovizual.init();
	} );
