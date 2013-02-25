from django.db import models

# Create your models here.

class Problem(models.Model):
        title = models.CharField(max_length = 255)

	def __unicode__(self):
	        return self.title


class Variable(models.Model):
	var_id = models.CharField(max_length = 255, verbose_name="Variable")
	var_value = models.TextField(verbose_name="Variable Value")
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

	 

