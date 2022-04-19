<?php

namespace Drupal\invite_by_email\Plugin\Invite;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\invite\InvitePluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class for Invite by Email.
 *
 * @Plugin(
 *   id="invite_by_email",
 *   label = @Translation("Invite By Email")
 * )
 */
class InviteByEmail extends PluginBase implements InvitePluginInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs invite_by_email plugin.
   *
   * @param array $configuration
   *   Array with configurations.
   * @param string $plugin_id
   *   The plugin id.
   * @param string $plugin_definition
   *   Plugin Definition.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MessengerInterface $messenger,
    MailManagerInterface $mail_manager,
    LanguageManagerInterface $language_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->messenger        = $messenger;
    $this->mailManager      = $mail_manager;
    $this->languageManager  = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('messenger'),
      $container->get('plugin.manager.mail'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function send($invite) {
    $module         = 'invite_by_email';
    $key            = $invite->get('type')->value;
    $to             = $invite->get('field_invite_email_address')->value;
    $from           = $invite->getOwner()->getEmail();
    $language_code  = $this->languageManager->getDefaultLanguage()->getId();
    $send_now       = TRUE;
    $params         = [
      'invite'  => $invite,
    ];

    $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);

    if ($result) {
      $this->messenger->addStatus($this->t('Invitation has been sent.'));
      \Drupal::logger('invite')->notice(
        'Invitation has been sent for: @mail_user.', ['@mail_user' => $to]
      );
    }
    else {
      $this->messenger->addStatus($this->t('Failed to send a message.'), 'error');
      \Drupal::logger('invite')->error('Failed to send a message.');
    }
  }

}
