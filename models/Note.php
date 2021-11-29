<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "notes".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $text
 * @property string|null $publication_date
 * @property int|null $user_id
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Note extends \yii\db\ActiveRecord
{
    static $errors = [];

    const QUANTITY_PER_PAGE = 5;

    const LIMIT_ERROR = 1;
    const FORBIDDEN_ERROR = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['publication_date', 'user_id', 'title'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['publication_date'], 'dateHandling'],
            [['text'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return string[]
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'text' => 'Text',
            'publication_date' => 'Publication Date',
            'user_id' => 'Author By',
        ];
    }

    /**
     * Constraints list
     * @param int $type
     * @return string
     */
    public function getError(int $type): string
    {
        return [
            self::LIMIT_ERROR => 'You cannot update or delete notes after
                24 hours have passed since they were created.',
            self::FORBIDDEN_ERROR => 'You do not have permission to update or delete a note.'
        ][$type];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return bool
     */
    public function checkForConstraints()
    {
        if (!$this->checkForTimeLimits()) {
            static::$errors[] = $this->getError(self::LIMIT_ERROR);
        }

        if (!$this->authorVerification()) {
            static::$errors[] = $this->getError(self::FORBIDDEN_ERROR);
        }

        return $this->checkForTimeLimits() && $this->authorVerification();
    }

    /**
     * @return bool
     */
    public function checkForTimeLimits()
    {
        return (time() - $this->created_at) < 60 * 60 * 24;
    }

    /**
     * Check if user has Access
     * @return bool
     */
    public function authorVerification()
    {
        return $this->user_id == Yii::$app->user->id;
    }

    /**
     * Get all notes
     * @param int $page
     * @return array
     */
    public static function getAllNotes(int $page): array
    {
        $offset = (self::QUANTITY_PER_PAGE * $page) - self::QUANTITY_PER_PAGE;
        $todayDate = date('Y-m-d', time());

        $notes = self::find()
            ->select('title, text, publication_date')
            ->where(['<=', 'publication_date', $todayDate]);

        if (Yii::$app->user->id) {
            $notes = $notes->orWhere(['user_id' => Yii::$app->user->id]);
        }

        $notes = $notes->orderBy([
            'publication_date' => SORT_DESC,
            'created_at' => SORT_DESC
        ]);

        $counts = self::getCounts($notes);
        $notes = $notes->offset($offset)
            ->limit(self::QUANTITY_PER_PAGE)
            ->asArray()
            ->all();

        return array_merge(
            $notes,
            $counts,
            ['currentPage' => $page]
        );
    }

    /**
     * @param $activeQuery
     * @return array
     */
    public static function getCounts($activeQuery)
    {
        $query = clone $activeQuery;

        $queryCount = $query->count();

        return [
            'count' => $queryCount,
            'pageCount' => $queryCount == 0 ? 0 : ceil($queryCount / self::QUANTITY_PER_PAGE)
        ];

    }

    /**
     * Get note by ID
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getNoteById($id)
    {
        $todayDate = date('Y-m-d', time());

        $note = self::find()
            ->select('title, text, publication_date, user_id')
            ->joinWith(['user' => function ($user) {
                $user->select('username');
            }])
            ->where(['notes.id' => $id])
            ->andWhere(['<=', 'publication_date', $todayDate]);

        if (Yii::$app->user->id) {
            $note = $note->orWhere(['user_id' => Yii::$app->user->id]);
        }

        return $note->asArray()->one();
    }

    /**
     * Create model
     * @param $data
     * @return bool
     */
    public function create(array $data)
    {
        $this->user_id = Yii::$app->user->id;
        $this->title = $data['title'] ?? '';
        $this->text = $data['text'] ?? '';
        $this->publication_date = $data['publication_date'] ?? '';

        return $this->save();
    }

    /**
     * Model edit
     * @param $data
     * @return bool
     */
    public function edit($data)
    {
        $this->user_id = $this->setValue(Yii::$app->user->id, $this->user_id);
        $this->title = $this->setValue($data['title'], $this->title);
        $this->text = $this->setValue($data['text'], $this->text);
        $this->publication_date = $this->setValue($data['publication_date'], $this->publication_date);

        return $this->save();
    }

    /**
     * Check date format
     */
    public function dateHandling()
    {
        $date = strtotime($this->publication_date);

        if (!$date) {
            $this->addError('publication_date', 'Invalid date');
        } else {
            $this->publication_date = date('Y-m-d', $date);
        }
    }

    /**
     * @param $value
     * @param string $or
     * @return mixed|string
     */
    public function setValue($value, $or = '')
    {
        return $value ?? $or;
    }

    /**
     * @param int $id
     * @return Note|null
     */
    public static function findModel(int $id)
    {
        return self::findOne($id);
    }

    /**
     * @return array
     */
    public function getResponseColumns()
    {
        return [
            'title' => $this->title,
            'text' => $this->text,
            'publication_date' => $this->publication_date
        ];
    }
}
