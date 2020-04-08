<?php
/**
 * jBackend content plugin for Joomla
 *
 * @author selfget.com (info@selfget.com)
 * @package jBackend
 * @copyright Copyright 2014 - 2015
 * @license GNU Public License
 * @link http://www.selfget.com
 * @version 2.1.3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJBackendContent extends JPlugin
{
  public function __construct(& $subject, $config)
  {
    parent::__construct($subject, $config);
    $this->loadLanguage();
  }

  public static function generateError($errorCode)
  {
    $error = array();
    $error['status'] = 'ko';
    switch($errorCode) {
      case 'REQ_ANS':
        $error['error_code'] = 'REQ_ANS';
        $error['error_description'] = 'Action not specified';
        break;
      case 'CNT_ANF':
        $error['error_code'] = 'CNT_ANF';
        $error['error_description'] = 'Article not found';
        break;
      case 'CNT_ANF':
        $error['error_code'] = 'CNT_AGE';
        $error['error_description'] = 'Article generic error';
        break;
      case 'CNT_ANA':
        $error['error_code'] = 'CNT_ANA';
        $error['error_description'] = 'Access not authorized';
        break;
      case 'CNT_CNF':
        $error['error_code'] = 'CNT_CNF';
        $error['error_description'] = 'Category not found';
        break;
      case 'CNT_NCF':
        $error['error_code'] = 'CNT_NCF';
        $error['error_description'] = 'No categories found';
        break;
      case 'CNT_TNS':
        $error['error_code'] = 'CNT_TNS';
        $error['error_description'] = 'Tag not specified';
        break;
      case 'CNT_TGE':
        $error['error_code'] = 'CNT_TGE';
        $error['error_description'] = 'Tag generic error';
        break;

    }
    return $error;
  }

  /**
   *
   * Build and return the (called) prefix (e.g. http://www.youdomain.com) from the current server variables
   *
   * We say 'called' 'cause we use HTTP_HOST (taken from client header) and not SERVER_NAME (taken from server config)
   *
   */
  private static function getPrefix()
  {
    if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
      $https = 's://';
    } else {
      $https = '://';
    }
    return 'http' . $https . $_SERVER['HTTP_HOST'];
  }

  /**
   *
   * Build and return the (called) base path for site (e.g. http://www.youdomain.com/path/to/site)
   *
   * @param  boolean  If true returns only the path part (e.g. /path/to/site)
   *
   */
  private static function getBase($pathonly = false)
  {
    if ( (strpos(php_sapi_name(), 'cgi') !== false) && (!ini_get('cgi.fix_pathinfo')) && (strlen($_SERVER['REQUEST_URI']) > 0) ) {
      // PHP-CGI on Apache with "cgi.fix_pathinfo = 0"

      // We use PHP_SELF
      if (@strlen(trim($_SERVER['PATH_INFO'])) > 0) {
        $p = strrpos($_SERVER['PHP_SELF'], $_SERVER['PATH_INFO']);
        if ($p !== false) { $s = substr($_SERVER['PHP_SELF'], 0, $p); }
      } else {
        $p = $_SERVER['PHP_SELF'];
      }
      $base_path = trim(rtrim(dirname(str_replace(array('"', '<', '>', "'"), '', $p)), '/\\'));
      // Check if base path was correctly detected, or use another method
      /*
         On some Apache servers (mainly using cgi-fcgi) it happens that the base path is not correctly detected.
         For URLs like http://www.site.com/index.php/content/view/123/5 the server returns a wrong PHP_SELF variable.

         WRONG:
         [REQUEST_URI] => /index.php/content/view/123/5
         [PHP_SELF] => /content/view/123/5

         CORRECT:
         [REQUEST_URI] => /index.php/content/view/123/5
         [PHP_SELF] => /index.php/content/view/123/5

         And this lead to a wrong result for JUri::base function.

         WRONG:
         JUri::base(true) => /content/view/123
         JUri::base(false) => http://www.site.com/content/view/123/

         CORRECT:
         getBase(true) =>
         getBase(false):http://www.site.com/
      */
      if (strlen($base_path) > 0) {
        if (strpos($_SERVER['REQUEST_URI'], $base_path) !== 0) {
          $base_path = trim(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
        }
      }
    } else {
      // We use SCRIPT_NAME
      $base_path = trim(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
    }

    return $pathonly === false ? self::getPrefix() . $base_path . '/' : $base_path;
  }

  public static function generateImagesFields($images, $add_siteurl = false)
  {
    $fields = array('image_intro' => '', 'float_intro' => '', 'image_intro_alt' => '', 'image_intro_caption' => '',
      'image_fulltext' => '', 'float_fulltext' => '', 'image_fulltext_alt' => '', 'image_fulltext_caption' => '');

    $decoded = json_decode($images, true);
    $decoded = (is_array($decoded))? array_merge($fields, $decoded) : $fields;

    if ($add_siteurl)
    {
      $base = self::getBase();
      if (strlen($decoded['image_intro']) > 0) $decoded['image_intro'] = $base . $decoded['image_intro'];
      if (strlen($decoded['image_fulltext']) > 0) $decoded['image_fulltext'] = $base . $decoded['image_fulltext'];
    }

    return $decoded;
  }

  private static function matchImgTag($matches, $set= false)
  {
    static $base = '';

    if ($set)
    {
      $base = self::getBase();
      return;
    }

    if (strpos($matches[2], 'http') !== 0)
    {
      $matches[2] = ltrim($matches[2], '/');
      return $matches[1] . $base . $matches[2] . $matches[3];
    }
    return $matches[0];
  }

  public static function forceFullImgSrc($content)
  {
    $pattern = '|(<img .*src=["\'])([^"\']*)(["\'] .*/>)|imsU';
    $m = array(); self::matchImgTag($m, true); // Set the base path
    $content = preg_replace_callback($pattern, 'plgJBackendContent::matchImgTag', $content);
    return $content;
  }

  public function getArticle($id, $filter_language = false, $filter_state = false, $filter_access = true, &$data = null)
  {
    $result = true;

    $user = JFactory::getUser();

    $id = (int) $id;

    try
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true)
        ->select(
            'a.id, a.asset_id, a.title, a.alias, a.introtext, a.fulltext, ' .
            // If badcats is not null, this means that the article is inside an unpublished category
            // In this case, the state is set to 0 to indicate Unpublished (even if the article state is Published)
            'CASE WHEN badcats.id is null THEN a.state ELSE 0 END AS state, ' .
            'a.catid, a.created, a.created_by, a.created_by_alias, ' .
            // Use created if modified is 0
            'CASE WHEN a.modified = ' . $db->quote($db->getNullDate()) . ' THEN a.created ELSE a.modified END as modified, ' .
            'a.modified_by, a.checked_out, a.checked_out_time, a.publish_up, a.publish_down, ' .
            'a.images, a.urls, a.attribs, a.version, a.ordering, ' .
            'a.metakey, a.metadesc, a.access, a.hits, a.metadata, a.featured, a.language, a.xreference'
        );
      $query->from('#__content AS a');

      // Join on category table
      $query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access')
        ->join('LEFT', '#__categories AS c on c.id = a.catid');

      // Join on user table
      $query->select('u.name AS author')
        ->join('LEFT', '#__users AS u on u.id = a.created_by');

      // Filter by language
      if ($filter_language)
      {
        $query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
      }

      // Join over the categories to get parent category titles
      $query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias')
        ->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');

      // Join on voting table
      $query->select('ROUND(v.rating_sum / v.rating_count, 0) AS rating, v.rating_count as rating_count')
        ->join('LEFT', '#__content_rating AS v ON a.id = v.content_id')

        ->where('a.id = ' . (int) $id);

      if ((!$user->authorise('core.edit.state', 'com_content')) && (!$user->authorise('core.edit', 'com_content'))) {
        // Filter by start and end dates
        $nullDate = $db->quote($db->getNullDate());
        $date = JFactory::getDate();

        $nowDate = $db->quote($date->toSql());

        $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
          ->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
      }

      // Join to check for category published state in parent categories up the tree
      // If all categories are published, badcats.id will be null, and we just use the article state
      $subquery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
      $subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
      $subquery .= 'WHERE parent.extension = ' . $db->quote('com_content');
      $subquery .= ' AND parent.published <= 0 GROUP BY cat.id)';
      $query->join('LEFT OUTER', $subquery . ' AS badcats ON badcats.id = c.id');

      // Filter by published state
      if (is_numeric($filter_state))
      {
        $query->where('(a.state = ' . (int) $filter_state . ')');
      }

      $db->setQuery($query);

      $data = $db->loadObject();

      if (empty($data))
      {
        return 'CNT_ANF'; // Article not found
      }

      // Check for published state if filter set
      if ( (is_numeric($filter_state)) && ($data->state != $filter_state) )
      {
        return 'CNT_ANF'; // Article not found
      }

      // Convert parameter fields to objects
      $registry = new JRegistry;
      $registry->loadString($data->attribs);
      $data->params = $registry;

      $registry = new JRegistry;
      $registry->loadString($data->metadata);
      $data->metadata = $registry;

      // Technically guest could edit an article, but lets not check that to improve performance a little
      if (!$user->get('guest'))
      {
        $userId = $user->get('id');
        $asset = 'com_content.article.' . $data->id;

        // Check general edit permission first
        if ($user->authorise('core.edit', $asset))
        {
          $data->params->set('access-edit', true);
        }

        // Now check if edit.own is available
        elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
        {
          // Check for a valid user and that they are the owner
          if ($userId == $data->created_by)
          {
            $data->params->set('access-edit', true);
          }
        }
      }

      // Compute view access permissions
      if ($filter_access)
      {
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();

        if ($data->catid == 0 || $data->category_access === null)
        {
          $data->params->set('access-view', in_array($data->access, $groups));
        }
        else
        {
          $data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
        }
      } else {
        $data->params->set('access-view', true);
      }

    }
    catch (Exception $e)
    {
      $result = 'CNT_AGE'; // Article generic error
    }

    return $result;

  }

  public function actionCategories(&$response, &$status = null)
  {
    $app = JFactory::getApplication();

    $id = $app->input->getInt('id');
    if (is_null($id))
    {
      // Get the list of categories
      $rootid = $app->input->getInt('rootid');
      if (is_null($rootid)) { $rootid = 'root'; }

      $recursive = $app->input->getString('recursive');
      $recursive = (($recursive == 'true') || ($recursive == '1'));

      $countitems = $app->input->getString('countitems');
      $countitems = (($countitems == 'true') || ($countitems == '1'));

      $options = array();
      $options['countItems'] = $countitems;
      $categories = JCategories::getInstance('Content', $options);

      $cats = new JObject;
      $cats->_parent = $categories->get($rootid);
      if (is_object($cats->_parent))
      {
        $cats->_items = $cats->_parent->getChildren($recursive);
      } else {
        $response = self::generateError('CNT_NCF'); // No categories found
        return false;
      }
      $response['status'] = 'ok';
      $response['total'] = count($cats->_items);
      $response['categories'] = array();
      foreach ($cats->_items as $cat)
      {
        $item = array();
        $item['id'] = $cat->id;
        $item['title'] = $cat->title;
        $item['description'] = $cat->description;
        $item['parent_id'] = $cat->parent_id;
        $item['numitems'] = $cat->numitems;
        $response['categories'][] = $item;
      }
      return true;
    } else {
      // Get a category
      $options = array();
      $options['countItems'] = true;
      $categories = JCategories::getInstance('Content', $options);

      $cats = new JObject;
      $cats->_item = $categories->get($id);
      if (is_null($cats->_item))
      {
        $response = self::generateError('CNT_CNF'); // Category not found
        return false;
      }
      $response['status'] = 'ok';
      $response['id'] = $cats->_item->id;
      $response['title'] = $cats->_item->title;
      $response['alias'] = $cats->_item->alias;
      $response['description'] = $cats->_item->description;
      $response['metadesc'] = $cats->_item->metadesc;
      $response['metakey'] = $cats->_item->metakey;
      $response['metadata'] = json_decode($cats->_item->metadata);
      $response['language'] = $cats->_item->language;
      $response['parent_id'] = $cats->_item->parent_id;
      $response['level'] = $cats->_item->level;
      $response['numitems'] = $cats->_item->numitems;
      return true;
    }
  }

  public function buildContentOrderBy($orderby, $orderdir)
  {
    // id, title, alias, catid, state, created, created_by, ordering (default), hits
    $allowed = array(
        'id' =>'a.id',
        'title' => 'a.title',
        'alias' => 'a.alias',
        'catid' => 'a.catid',
        'state' => 'a.state',
        'created' => 'a.created',
        'created_by' => 'a.created_by',
        'ordering' => 'a.ordering',
        'hits' => 'a.hits'
      );
    $orderCol = (array_key_exists($orderby, $allowed)) ? $allowed[$orderby] : 'a.ordering';
    return $orderCol;
  }

  public function buildContentOrderDir($orderby, $orderdir)
  {
    // asc (default), desc
    $allowed = array(
        'asc' =>'ASC',
        'desc' => 'DESC'
      );
    $listOrder = (array_key_exists($orderdir, $allowed)) ? $allowed[$orderdir] : 'ASC';
    return $listOrder;
  }

  public function actionArticles(&$response, &$status = null)
  {
  	$user = JFactory::getUser();
  	$response['user'] = $user->location;
    $app = JFactory::getApplication();
    $tags = new JHelperTags;
    $id = $app->input->getInt('id');
    if (is_null($id))
    {
      // Get the list of articles
      $catid = $app->input->getInt('catid');
      $maxsubs = $app->input->getInt('maxsubs');
      $featured = $app->input->getString('featured');
      $limit = $app->input->getInt('limit');
      $offset = $app->input->getInt('offset');
      $orderby = $app->input->getString('orderby');
      $orderdir = $app->input->getString('orderdir');

      $filter_language = $this->params->get('filter_language', false);
      $filter_state = $this->params->get('filter_state', 1);
      $filter_access = $this->params->get('filter_access', true);
      $full_image_url = $this->params->get('full_image_url', true);
      $force_full_image_url_in_content = $this->params->get('force_full_image_url_in_content', false);
      $add_tags_in_article_list = $this->params->get('add_tags_in_article_list', false);

      $options = array('countItems' => false);
      $categories = JCategories::getInstance('Content', $options);
      $category = $categories->get($catid);

      if (!is_null($category))
      {
        require_once JPATH_SITE . '/components/com_content/models/articles.php';
        $model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
        $model->setState('params', JFactory::getApplication()->getParams());
        $model->setState('filter.category_id', $category->id);
        if (is_numeric($filter_state))
        {
          $model->setState('filter.published', (int) $filter_state);
        }
        // $model->setState('filter.archived', $this->getState('filter.archived'));
        $model->setState('filter.access', $filter_access);
        $model->setState('filter.language', $filter_language);
        $model->setState('filter.featured', $featured);
        $model->setState('list.ordering', self::buildContentOrderBy($orderby, $orderdir));
        $model->setState('list.start', $offset);
        $model->setState('list.limit', $limit);
        $model->setState('list.direction', self::buildContentOrderDir($orderby, $orderdir));

        //$model->setState('list.filter', $this->getState('list.filter'));
        // filter.subcategories indicates whether to include articles from subcategories in the list or blog
        $model->setState('filter.subcategories', (boolean) ($maxsubs > 0));
        $model->setState('filter.max_category_levels', (int) $maxsubs);
        //$model->setState('list.links', $this->getState('list.links'));
        if ($limit >= 0)
        {
          $articles = $model->getItems();

          if ($articles === false)
          {
          $response = self::generateError('CNT_AGE'); // Article generic error
          return false;
          }
        } else {
          $articles = array();
        }

        $pagination = $model->getPagination();
      }
      $response['status'] = 'ok';
      $response['total'] = count($articles);
      $response['limit'] = $pagination->limit;
      $response['offset'] = $pagination->limitstart;
      $response['pages_current'] = $pagination->pagesCurrent;
      $response['pages_total'] = $pagination->pagesTotal;
      $response['articles'] = array();
      foreach ($articles as $article)
      {
        $item = array();
        $item['id'] = $article->id;
        $item['title'] = $article->title;
        $item['alias'] = $article->alias;
        $item['featured'] = $article->featured;
        $item['content'] = (trim($article->fulltext) != '') ? $article->introtext . $article->fulltext : $article->introtext;
        $item['content'] = JHtml::_('content.prepare', $item['content']);
        if ($force_full_image_url_in_content) { $item['content'] = self::forceFullImgSrc($item['content']); }
        $item['images'] = self::generateImagesFields($article->images, $full_image_url);
        if ($add_tags_in_article_list) {
          // Get the article tags
          $item_tags = array();
          $tags->getItemTags('com_content.article', $article->id);
          foreach ($tags->itemTags as $tag) {
            $tag_element = array('id' => $tag->id, 'title' => $tag->title, 'alias' => $tag->alias, 'language' => $tag->language);
            $item_tags[] = $tag_element;
          }
          $item['tags'] = $item_tags;
        }
        $item['metadesc'] = $article->metadesc;
        $item['metakey'] = $article->metakey;
        $article->metadata = json_decode($article->metadata);
        if (!is_null($article->metadata))
        {
          $item['metadata']['robots'] = $article->metadata->robots;
          $item['metadata']['author'] = $article->metadata->author;
          $item['metadata']['rights'] = $article->metadata->rights;
          $item['metadata']['xreference'] = $article->metadata->xreference;
        }
        $item['category_title'] = $article->category_title;
        $item['author'] = ($article->created_by_alias) ? $article->created_by_alias : $article->author;
        $item['published_date'] = JHtml::_('date', $article->publish_up, 'Y-m-d H:i:s');
        $response['articles'][] = $item;
      }
      return true;
    } else {
      // Get an article
      $filter_language = $this->params->get('filter_language', false);
      $filter_state = $this->params->get('filter_state', 1);
      $filter_access = $this->params->get('filter_access', true);
      $full_image_url = $this->params->get('full_image_url', true);
      $force_full_image_url_in_content = $this->params->get('force_full_image_url_in_content', false);
      $result = $this->getArticle($id, $filter_language, $filter_state, $filter_access, $article);
      if ($result === true)
      {
        // Check the view access to the article
        $user = JFactory::getUser();
        if ($article->params->get('access-view') != true)
        {
          $response = self::generateError('CNT_ANA'); // Access not authorized
          return false;
        }
        // Get the article tags
        $item_tags = array();
        $tags = new JHelperTags;
        $tags->getItemTags('com_content.article', $id);
        foreach ($tags->itemTags as $tag) {
          $tag_element = array('id' => $tag->id, 'title' => $tag->title, 'alias' => $tag->alias, 'language' => $tag->language);
          $item_tags[] = $tag_element;
        }
        $response['status'] = 'ok';
        $response['id'] = $article->id;
        $response['title'] = $article->title;
        $response['alias'] = $article->alias;
        $response['featured'] = $article->featured;
        $response['content'] = (trim($article->fulltext) != '') ? $article->introtext . $article->fulltext : $article->introtext;
        $response['content'] = JHtml::_('content.prepare', $response['content']);
        if ($force_full_image_url_in_content) { $response['content'] = self::forceFullImgSrc($response['content']); }
        $response['images'] = self::generateImagesFields($article->images, $full_image_url);
        $response['tags'] = $item_tags;
        $response['metadesc'] = $article->metadesc;
        $response['metakey'] = $article->metakey;
        $response['metadata']['robots'] = $article->metadata->get('robots');
        $response['metadata']['author'] = $article->metadata->get('author');
        $response['metadata']['rights'] = $article->metadata->get('rights');
        $response['metadata']['xreference'] = $article->metadata->get('xreference');
        $response['language'] = $article->language;
        $response['category_title'] = $article->category_title;
        $response['author'] = ($article->created_by_alias) ? $article->created_by_alias : $article->author;
        $response['published_date'] = JHtml::_('date', $article->publish_up, 'Y-m-d H:i:s');
        $response['state'] = $article->state;
        return true;
      } else {
        $response = self::generateError($result);
        return false;
      }
    }
  }

  public function actionTagArticles(&$response, &$status = null)
  {
    $app = JFactory::getApplication();
    $tags = new JHelperTags;

    $tagid = $app->input->getString('tagid');
    $tagid_list = array_filter(array_map('trim', explode(",", $tagid)));

    if (count($tagid_list) > 0)
    {
      $limit = $app->input->getInt('limit');
      $offset = $app->input->getInt('offset');

      $tagid = implode(",", $tagid_list);

      $type = new JUcmType;
      $typesr = $type->getTypeByAlias('com_content.article')->type_id;
      $includeChildren = false;
      $orderByOption = 'c.core_title';
      $orderDir = 'ASC';
      $anyOrAll = true;
      $languageFilter = 'all';
      $stateFilter = '0,1';

      $full_image_url = $this->params->get('full_image_url', true);
      $force_full_image_url_in_content = $this->params->get('force_full_image_url_in_content', false);
      $add_tags_in_article_list = $this->params->get('add_tags_in_article_list', false);

      $db = JFactory::getDBO();
      $query = $tags->getTagItemsQuery($tagid, $typesr, $includeChildren, $orderByOption, $orderDir, $anyOrAll, $languageFilter, $stateFilter);

      $query_count = clone $query;
      $query_count->clear('select');
      $query_count->select('COUNT(*)');

      $db->setQuery($query_count);
      $count = $db->loadResult();
      if ($db->getErrorNum())
      {
        $response = self::generateError('CNT_TGE'); // Tag generic error
        return false;
      }

      if ($limit > $count) { $offset = 0; }

      if ($offset > $count - $limit)
      {
        $offset = max(0, (int) (ceil($count / $limit) - 1) * $limit);
      }

      if ($limit > 0)
      {
        $pagesTotal = ceil($count / $limit);
        $pagesCurrent = ceil(($offset + 1) / $limit);
      }

      $query->select('cnt.*');
      $query->select('cats.title AS category_title');
      $query->join('INNER', '#__content AS cnt ON cnt.id = m.content_item_id');
      $query->join('INNER', '#__categories AS cats ON cats.id = cnt.catid');
      $db->setQuery($query, $offset, $limit);
      $articles = $db->loadObjectList();
      if ($db->getErrorNum())
      {
        $response = self::generateError('CNT_TGE'); // Tag generic error
        return false;
      }

      $response['status'] = 'ok';
      $response['total'] = count($articles);
      $response['limit'] = $limit;
      $response['offset'] = $offset;
      $response['pages_current'] = $pagesCurrent;
      $response['pages_total'] = $pagesTotal;
      $response['articles'] = array();
      foreach ($articles as $article)
      {
        $item = array();
        $item['id'] = $article->id;
        $item['title'] = $article->title;
        $item['alias'] = $article->alias;
        $item['featured'] = $article->featured;
        $item['content'] = (trim($article->fulltext) != '') ? $article->introtext . $article->fulltext : $article->introtext;
        $item['content'] = JHtml::_('content.prepare', $item['content']);
        if ($force_full_image_url_in_content) { $item['content'] = self::forceFullImgSrc($item['content']); }
        $item['images'] = self::generateImagesFields($article->images, $full_image_url);
        if ($add_tags_in_article_list) {
          // Get the article tags
          $item_tags = array();
          $tags->getItemTags('com_content.article', $article->id);
          foreach ($tags->itemTags as $tag) {
            $tag_element = array('id' => $tag->id, 'title' => $tag->title, 'alias' => $tag->alias, 'language' => $tag->language);
            $item_tags[] = $tag_element;
          }
          $item['tags'] = $item_tags;
        }
        $item['metadesc'] = $article->metadesc;
        $item['metakey'] = $article->metakey;
        $article->metadata = json_decode($article->metadata);
        if (!is_null($article->metadata))
        {
          $item['metadata']['robots'] = $article->metadata->robots;
          $item['metadata']['author'] = $article->metadata->author;
          $item['metadata']['rights'] = $article->metadata->rights;
          $item['metadata']['xreference'] = $article->metadata->xreference;
        }
        $item['category_title'] = $article->category_title;
        $item['author'] = ($article->created_by_alias) ? $article->created_by_alias : $article->author;
        $item['published_date'] = JHtml::_('date', $article->publish_up, 'Y-m-d H:i:s');
        $response['articles'][] = $item;
      }
      return true;
    } else {
      $response = self::generateError('CNT_TNS'); // Tag not specified
      return false;
    }
  }

  /**
   * Fulfills requests for content module
   *
   * @param   object    $module      The module invoked
   * @param   object    $response    The response generated
   * @param   object    $status      The boundary conditions (e.g. authentication status)
   *
   * @return  boolean   true if there are no problems (status = ok), false in case of errors (status = ko)
   */
  public function onRequestContent($module, &$response, &$status = null)
  {
    if ($module !== 'content') return true;

    // Add to module call stack
    jBackendHelper::moduleStack($status, 'content');

    $app = JFactory::getApplication();
    $action = $app->input->getString('action');

    if (is_null($action)) {
      $response = self::generateError('REQ_ANS'); // Action not specified
      return false;
    }

    $resource = $app->input->getString('resource');

    switch ($resource)
    {
      case 'categories':
        if ($action == 'get')
        {
          return $this->actionCategories($response, $status);
        }
        break;
      case 'articles':
        if ($action == 'get')
        {
          return $this->actionArticles($response, $status);
        }
        break;
      case 'tagarticles':
        if ($action == 'get')
        {
          return $this->actionTagArticles($response, $status);
        }
        break;
    }

    return true;
  }
}
