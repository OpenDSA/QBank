from django.db import models

# Create your models here.

class Problem(models.Model):
	title = models.CharField(max_length = 255, verbose_name="Problem Name")
	type = models.CharField(max_length = 255)
	DIFFICULTY = (
		('EASY', 'EASY'),
		('MEDIUM', 'MEDIUM'),
		('HARD', 'HARD'),
		('VERY HARD', 'VERY HARD'),
	)
	difficulty_level = models.CharField(max_length=255, choices=DIFFICULTY, default='EASY')
	
	def __unicode__(self):
	        return self.title

class CommonIntroduction(models.Model):
	common_intro = models.TextField(verbose_name="Introduction")
	problem = models.ForeignKey(Problem)
	def __unicode__(self):
		return self.var_id + "(" + str(self.problem) + ")"

class Variable(models.Model):
	var_id = models.CharField(max_length = 255, verbose_name="Variable Name")
	var_value = models.TextField(verbose_name="Variable Value")
	attribute = models.TextField(verbose_name="Attribute", help_text="Type=Value", blank= True)
	problem = models.ForeignKey(Problem)
	def __unicode__(self):
		return self.var_id + "(" + str(self.problem) + ")"

class ProblemTemplate(models.Model):
	question = models.TextField()
	problem = models.ForeignKey(Problem)

	def __unicode__(self):
		return "(" + str(self.problem) + ")"


class Answer(models.Model):
	solution = models.TextField()
	problem = models.ForeignKey(Problem)

	def __unicode__(self):
		return "(" + str(self.problem) + ")"

class Choice(models.Model):
	choice = models.TextField()
	problem = models.ForeignKey(Problem)

	def __unicode__(self):
		return "(" +str(self.problem) + ")"

class Hint(models.Model):
	hint = models.TextField()
	problem = models.ForeignKey(Problem)
	def __unicode__(self):
		return "(" +str(self.problem) + ")"

class Script(models.Model):
	script = models.TextField(help_text="Use &lt;script&gt;&lt;/script&gt; ")
	problem = models.ForeignKey(Problem)
	def __unicode__(self):
		return "(" +str(self.problem) + ")"


	 

