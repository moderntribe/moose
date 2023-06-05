/**
 * Dependencies
 */
import React, { useState, useEffect } from 'react';
const { __ } = wp.i18n;
const { Button, Spinner } = wp.components;
/**
 * Build the Recaptcha Preview
 * @returns {object} Captcha Preview.
 */
export default function CaptchaPreview(props) {

    const [verified, setVerified] = useState(false);
    const [failure, setFailure] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [v2Processing, setV2Processing] = useState(false);
    const [turnstileProcessing, setTurnstileProcessing] = useState(false);
    const [version, setVersion] = useState(0);
    const [siteKey, setSiteKey] = useState('');
    const [secret, setSecret] = useState('');

    const handleSubmit = (e) => {
        setProcessing(true);
        e.preventDefault();


        const script = document.createElement("script");
        if (2 == version) {
            script.onload = turnstileHandleScriptLoad;
            script.src = "https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit";
        } else if (1 == version) {
            script.onload = v3HandleScriptLoad;
            script.src = "https://www.google.com/recaptcha/api.js?render="+siteKey;
        } else {
            script.onload = v2HandleScriptLoad;
            script.src = "https://www.google.com/recaptcha/api.js?render=explicit";
        }
        document.body.appendChild(script);
    }

    const getRecaptchaSettings = function() {
        var siteKey = '';
        var secret = '';
        var version = ( undefined !== props.settings['enable_v3'] ? props.settings['enable_v3'] : undefined );
        if( version == 2) {
            siteKey = ( undefined !== props.settings['turnstile_site_key'] ? props.settings['turnstile_site_key'] : undefined );
        } else if ( version == 1) {
            siteKey = ( undefined !== props.settings['v3_re_site_key'] ? props.settings['v3_re_site_key'] : undefined );
        } else {
            siteKey = ( undefined !== props.settings['kt_re_site_key'] ? props.settings['kt_re_site_key'] : undefined );
        }
        if( version == 2) {
            secret = ( undefined !== props.settings['turnstile_secret_key'] ? props.settings['turnstile_secret_key'] : undefined );
        } else if ( version == 1) {
            secret = ( undefined !== props.settings['v3_re_secret_key'] ? props.settings['v3_re_secret_key'] : undefined );
        } else {
            secret = ( undefined !== props.settings['kt_re_secret_key'] ? props.settings['kt_re_secret_key'] : undefined );
        }

        return {
            version: version,
            siteKey: siteKey,
            secret: secret,
        }
    }

    const v2HandleScriptLoad = function() {
        setV2Processing(true);
        window.grecaptcha.ready(_ => {
            //wrapper promise to catch recaptcha errors
            const promise = new Promise((resolve, reject) => {
                grecaptcha.render('captcha_element', {
                    'sitekey' : siteKey,
                    'callback' : function(token) {
                        let ajax_url = window.location.origin + '/wp-admin/admin-ajax.php';
                        jQuery.ajax({
                            url : ajax_url,
                            type : 'POST',
                            data : { 
                                'action' : 'kadence_verify_recaptcha',
                                'g-recaptcha-response'	 : token,
                                'secret'	 : secret,
                                'version'	 : version,
                            }, 
                            dataType : 'json',
                            success : function( ktrespr ) {
                                if ( ktrespr.success ) {
                                    setVerified(true);
                                } else {
                                    setV2Processing(false);
                                    setFailure(true);
                                }
                            },
                            error : function( errorThrown ) {
                                setFailure(true);
                            },
                            always : function() {
                                setV2Processing(false);
                                setProcessing(false);
                            }
                        })
                    }
                })
            })
            .catch(error => {
                setFailure(true);
                setV2Processing(false);
                setProcessing(false);
            });
        })
    }

    const v3HandleScriptLoad = function() {
        window.grecaptcha.ready(_ => {
            //wrapper promise to catch recaptcha errors
            const promise = new Promise((resolve, reject) => {
                window.grecaptcha
                .execute(siteKey, { action: "homepage" })
                .then(token => {
                    // Ajax for verifying response through the secret key
                    let ajax_url = window.location.origin + '/wp-admin/admin-ajax.php';
                    jQuery.ajax({
                        url : ajax_url,
                        type : 'POST',
                        data : { 
                            'action' : 'kadence_verify_recaptcha',
                            'g-recaptcha-response'	 : token,
                            'secret'	 : secret,
                            'version'	 : version,
                        }, 
                        dataType : 'json',
                        success : function( ktrespr ) {
                            if ( ktrespr.success ) {
                                setVerified(true);
                                setProcessing(false);
                            } else {
                                setFailure(true);
                                setProcessing(false);
                            }
                        },
                        error : function( errorThrown ) {
                            setFailure(true);
                            setProcessing(false);
                        },
                        always : function() {
                            setProcessing(false);
                        }
                    })
                })
            })
            .catch(error => {
                setFailure(true);
                setProcessing(false);
            });
        })
    }

    const turnstileHandleScriptLoad = function() {
        setTurnstileProcessing(true);
        const promise = new Promise((resolve, reject) => {
            window.turnstile
            .render('#captcha_element', {
                sitekey: siteKey,
                action: 'verify',
                callback: function(token) {
                    let ajax_url = window.location.origin + '/wp-admin/admin-ajax.php';
                    jQuery.ajax({
                        url : ajax_url,
                        type : 'POST',
                        data : { 
                            'action' : 'kadence_verify_recaptcha',
                            'cf-turnstile-response'	 : token,
                            'secret'	 : secret,
                            'version'	 : version,
                        }, 
                        dataType : 'json',
                        success : function( ktrespr ) {
                            if ( ktrespr.success ) {
                                setVerified(true);
                            } else {
                                setTurnstileProcessing(false);
                                setFailure(true);
                            }
                        },
                        error : function( errorThrown ) {
                            setFailure(true);
                        },
                        always : function() {
                            setTurnstileProcessing(false);
                            setProcessing(false);
                        }
                    })
                }
            })
        })
        .catch(error => {
            setFailure(true);
            setProcessing(false);
        });
    }

    const resetValidation = function() {
        setVerified(false)
        setFailure(false)
        setProcessing(false)
        setV2Processing(false)
        setTurnstileProcessing(false)
    }

    useEffect(() => {
        var recaptchaSettings = getRecaptchaSettings();
        setVersion(recaptchaSettings.version);
        setSiteKey(recaptchaSettings.siteKey);
        setSecret(recaptchaSettings.secret);
        resetValidation()
    },
    [props.settings]);

    let errorHTML = ""
    if (failure) {
        errorHTML = <div class="kadence-captcha-preview-message error">
            Error in verification. Please check your keys and domains then try again.
        </div>;
    }
    let previewHTML = ""
    if (version == 0 || version == 2){
        previewHTML = <div class="kadence-captcha-preview">
            <div id="captcha_element"></div>
        </div>
    }

    if (verified) {
        return (
            <div class="kadence-captcha-preview-message success">Verified! Your keys completed a captcha.</div>
        )
    } else if (failure) {
        return errorHTML
    } else {
        return (
            <div class="kadence-captcha-preview">
                {previewHTML}
                { (!v2Processing && !turnstileProcessing) &&
                    <form action="#">
                        <Button
                            className="kadence-recapthca-verifiy"
                            variant="secondary"
                            onClick={ handleSubmit }
                            isBusy={processing}
                            label="verify recaptcha keys"
                        >
                            { processing ? __( 'Processing', 'kadence-settings' ) : __( 'Verify', 'kadence-settings' ) }
                        </Button>
                    </form>
                }
            </div>
        )
    }
}