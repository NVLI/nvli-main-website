<?php
/**
 * @file
 * Contains \Drupal\nvli_shorten\NvliShortenSubscriber.
 */
namespace Drupal\nvli_shorten\EventSubscriber;

use Drupal\Core\Entity\EntityTypeEvent;
use Drupal\Core\Entity\EntityTypeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
 * Class NvliShortenSubscriber.
 *
 * @package Drupal\nvli_shorten
 */
class NvliShortenSubscriber implements EventSubscriberInterface {
  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    print_r('hellow');
    $events[EntityTypeEvents::CREATE][] = array('shortenUrl', 800);
    return $events;
  }
  /**
   * Subscriber Callback for the event.
   * @param EntityTypeEvent $event
   */
  public function shortenUrl(EntityTypeEvent $event) {
    print_r('ffsssd');exit;
    print_r($event->getArguments());
    drupal_set_message("The Example Event has been subscribed, which has bee dispatched on submit of the form with " . $event->getEntityType() . " as Reference");
  }
}