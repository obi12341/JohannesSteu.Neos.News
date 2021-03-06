<?php
namespace JohannesSteu\Neos\News\Eel\FlowQueryOperations;

/*                                                                        *
 * This script belongs to the Flow package "JohannesSteu.Neos.News".      *
 *                                                                        */

use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * EEL operation to filter nodes by a given category.
 */
class FilterByCategoryOperation extends AbstractOperation
{
	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	protected static $shortName = 'filterByCategory';

	/**
	 * {@inheritdoc}
	 *
	 * @var integer
	 */
	protected static $priority = 100;

	/**
	 * {@inheritdoc}
	 *
	 * We can only handle TYPO3CR Nodes.
	 *
	 * @param mixed $context
	 * @return boolean
	 */
	public function canEvaluate($context)
	{
		return (isset($context[0]) && ($context[0] instanceof NodeInterface));
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param FlowQuery $flowQuery the FlowQuery object
	 * @param array $arguments First argument is the property to filter for, second is the category to filter for
	 * @return mixed
	 */
	public function evaluate(FlowQuery $flowQuery, array $arguments)
	{
		if (!isset($arguments[0]) || empty($arguments[0])) {
			throw new \TYPO3\Eel\FlowQuery\FlowQueryException('findByCategory() needs a category to filter for', 1332492263);
		} else {
			$nodesWithCategorySet = [];

			$categoryNode = $arguments[0];
			$nodes = $flowQuery->getContext();

			foreach($nodes as $node) {
				/** @var $node NodeInterface */
				$nodeCategories = $node->getProperty("categories");
				foreach ($nodeCategories as $nodeCategory) {

					if ($nodeCategory->getNodeType()->getName() !== 'JohannesSteu.Neos.News:Category') {
						throw new \TYPO3\Eel\FlowQuery\FlowQueryException('findByCategory() only accepts as parameter Nodes of type JohannesSteu.Neos.News:Category but '.$nodeCategory->getNodeType().' given', 1460741067);
					}

					/** @var $nodeCategory Node */
					if ($nodeCategory->getNodeData()->getProperty("uriPathSegment") == $categoryNode) {
						$nodesWithCategorySet[] = $node;
					}
				}

			}

			$flowQuery->setContext($nodesWithCategorySet);
		}
	}

}
